<?php

namespace App\Http\Controllers;

use App\Rules\MaxFileSize;
use Illuminate\Http\Request;

class FixController extends Controller
{
    public function index()
    {
        return view('fix', ['checked' => true]);
    }


    public function execute(Request $request)
    {
        $res = $this->getRows($request);
        $unique = $res['unique'];
        $correct_count = count(array_diff($res['rows'][0], array('')));
        $title = array_diff($res['rows'][0], array(''));
        array_shift($res['rows']);
        $result = $this->fixRows($res['rows'], $correct_count);
        $shift_count = $result['count'];
        $result = $result['rows'];

        if ($unique){
            $result = $this->rmNonUnique($result, $unique);
            $non_unique = $result['count'];
            $result = $result['rows'];
        }
        $result = array_merge([$title], $result);

        $xlsx = \SimpleXLSXGen::fromArray($result);
        $output_file = 'public/output/fixed_'.$res['file_name'];
        $xlsx->saveAs($output_file);

        return view('fix', [
            'checked' => false,
            'fixed' => true,
            'output_file' => $output_file,
            'shift_count' => $shift_count ?? 0,
            'non_unique' => $non_unique ?? 0
        ]);
    }


    public function check(Request $request)
    {
        $res = $this->getRows($request);
        $correct_count = count(array_diff($res['rows'][0], array('')));
        $shift = count($res['rows']) - $correct_count ?? 0;
        $shifted_rows = $this->getShiftRows($res['rows'], $correct_count);

        return view('fix', [
            'checked' => false,
            'title' => array_diff($res['rows'][0], array('')),
            'shift' => $shift,
            'shifted' => $shifted_rows,
            'file_name' => $res['file_name']
        ]);
    }


    public function getRows(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'file' => ['required', 'mimes:xlsx', new MaxFileSize()],
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


    public function fixRows($rows, $correct_count)
    {
        $fixed_rows = [];
        $fixed_count = 0;

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
                    $fixed_count++;
                }
                $shift = count($row) - $correct_count;
                while($shift){
                    array_shift($row);
                    $shift--;
                }
            }
            $fixed_rows[] = $row;
        }

        return ['rows' => $fixed_rows, 'count' => $fixed_count];
    }


    public function getShiftRows($rows, $correct_count)
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
