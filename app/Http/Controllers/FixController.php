<?php

namespace App\Http\Controllers;

use App\Rules\MaxFileSize;
use Illuminate\Http\Request;

class FixController extends Controller
{
    public function index()
    {
        return view('fix', ['checked' => false]);
    }


    public function execute(Request $request)
    {
        $file_name = $request->file('file')->getClientOriginalName();
        $correct_name = session('file_name');

        if ($correct_name != $file_name) {
            return redirect(route('main'))->withErrors('Please, choose the '.$correct_name);
        }


        $res = $this->getRows($request);
        $unique = $res['unique'];
        $shifted = (int)$request['shifted'];
        $correct_count = count(array_diff($res['rows'][0], array('')));
        $title = array_diff($res['rows'][0], array(''));
        /* remove title */
        array_shift($res['rows']);
        $result = $res['rows'];

        if (!$shifted && !$unique){

            return redirect(route('main'))->with('status','This file doesn\'t have shifted rows and You didn\'t choose removing non unique rows');
        }

        if($shifted){
            $result = $this->fixShiftedRows($result, $correct_count);
            $shifted = $result['shifted'];
            $result = $result['rows'];
        }
        if ($unique){
            $result = $this->rmNonUnique($result, $unique);
            $non_unique = $result['count'];
            $result = $result['rows'];
        }
        /* add title to array */
        $result = array_merge([$title], $result);

        $xlsx = \SimpleXLSXGen::fromArray($result);
        $output_file = 'public/output/fixed_'.$res['file_name'];
        $xlsx->saveAs($output_file);

        session_unset();

        return view('result', [
            'checked' => true,
            'fixed' => true,
            'output_file' => $output_file,
            'shift_count' => $shifted,
            'non_unique' => $non_unique ?? 0
        ]);
    }


    public function check(Request $request)
    {
        $res = $this->getRows($request);
        $correct_count = count(array_diff($res['rows'][0], array('')));
        $shift = count($res['rows'][0]) - $correct_count ?? 0;
        $shifted = $shift ? $this->getShiftedRows($res['rows'], $correct_count) : 0;

        session(['file_name' => $res['file_name']]);

        return view('fix', [
            'checked' => true,
            'title' => array_diff($res['rows'][0], array('')),
            'shift' => $shift,
            'shifted' => $shifted,
            'file_name' => $res['file_name']
        ]);
    }


    public function getRows(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'file' => ['required', 'max:2048', 'mimes:xlsx'],
            ]);
            $file = $request->file('file');
            $filename = $file->getPathname();
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $spreadsheet = $reader->load($filename);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            return [
                'rows' => $rows,
                'file_name' => $file->getClientOriginalName(),
                'unique' => $request['unique'] ?? null
            ];

        } else {

            return view('fix', ['checked' => true])->withErrors(['error' => "Wrong method"]);
        }
    }


    public function fixShiftedRows($rows, $correct_count)
    {
        $fixed_rows = [];
        $shifted = 0;

        foreach($rows as $row){

            if (!$row[$correct_count]){

                $shift = count($row) - $correct_count;
                while($shift){
                    array_pop($row);
                    $shift--;
                }
            } else {

                while(!end($row)){
                    array_pop($row);
                }
                $shift = count($row) - $correct_count;

                while($shift){
                    array_shift($row);
                    $shift--;
                }
                $shifted++;
            }
            $fixed_rows[] = $row;
        }

        return ['rows' => $fixed_rows, 'shifted' => $shifted];
    }


    public function getShiftedRows($rows, $correct_count)
    {
        $shifted_rows = 0;
        foreach($rows as $el){
            if ($el[$correct_count] != '' ) {
                $shifted_rows++;
            }
        }

        return $shifted_rows;
    }


    public function rmNonUnique($rows, $unique)
    {
        $unique_rows = [];
        $uniqs = [];
        $non_unique = 0;
        foreach($rows as $row) {
            if ( !in_array((string)$row[$unique], $uniqs )){
                $uniqs[] = (string)$row[$unique];
                $unique_rows[] = $row;
            } else {
                $non_unique++;
            }
        }

        return [ 'rows' => $unique_rows, 'count' => $non_unique ];
    }
}
