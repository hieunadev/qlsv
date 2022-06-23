<?php

namespace App\Repositories\Student;

use App\Models\Subject;
use App\Models\Student;
use App\Repositories\BaseRepository;
use Carbon\Carbon;

class StudentRepository extends BaseRepository implements StudentRepositoryInterface
{
    public function getModel()
    {
        return Student::class;
    }

    public function newStudent()
    {
        return new $this->model;
    }

    public function search($request)
    {
        $student = $this->model->with('subjects', 'faculty');

        // Age
        if (!empty($request['age_from'])) {
            $student->where('birthday', '<=', Carbon::now()->subYears($request['age_from']));
        }

        if (!empty($request['age_to'])) {
            $student->where('birthday', '>=', Carbon::now()->subYears($request['age_to']));
        }

        // Phone

        // $viettel = ['^037', '^038', '^039', '^036'];
        // $mobi = ['^070', '^079', '^078', '^077'];
        // $vina = ['^081', '^082', '^083', '^084'];

        // if (!empty($request['viettel'])) {
        //     $student->orwhere("phone", "REGEXP", implode("|", $viettel));
        // }
        // if (!empty($request['mobi'])) {
        //     $student->orWhere("phone", "REGEXP", implode("|", $mobi));
        // }
        // if (!empty($request['vina'])) {
        //     $student->orWhere("phone", "REGEXP", implode("|", $vina));
        // }

        $phones = [
            'viettel' =>'^037|^038|^039|^036',
            'vina' => '^070|^079|^078|^077',
            'mobi' => '^081|^082|^083|^084',
        ];

        if (!empty($request['viettel']) || !empty($request['vina']) || !empty($request['mobi'])) {
            $student->where(function ($query) use ($request, $phones) {
                foreach ($phones as $field => $phone) {
                    if (!empty($request[$field])) {
                        $query->orWhere('phone', 'regexp', $phone);
                    }
                }
            });
        }

        if (!empty($request['category'])) {

            $operator = '>=';

            if ($request['category'] == "2") {
                $operator = '<';
            }

            $student->whereHas('subjects', function ($q) {
                $q->where('point', '>', 0);
            }, $operator);
        }

        if (!empty($request['point_from']) && !empty($request['point_to'])) {
            $start = $request['point_from'];
            $end = $request['point_to'];

            $student->whereHas('subjects', function ($q) use ($start, $end) {
                $q->whereBetween('point', [$start, $end]);
            });
        }

        return $student->paginate(10);
    }
}
