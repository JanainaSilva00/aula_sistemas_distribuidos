<?php

class Student {
    const JSON_FILE_PATH = 'db.json';

    protected $studentData;

    protected $attributes = [
        'nome',
        'curso',
        'semestre',
        'ra',
        'cpf',
        'cidade'
    ];

    public function setData($data)
    {
        $this->studentData = $data;
    }

    protected function saveData($content)
    {
        try {
            $file = fopen(self::JSON_FILE_PATH, 'w');
            fwrite($file, json_encode($content));
            fclose($file);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function saveNewData()
    {
        $data = $this->getAllStudents();
        $this->saveData(array_merge($data, [$this->studentData]));
    }

    /**
     * @throws Exception
     */
    public function updateData()
    {
        $students = $this->getAllStudents();
        foreach ($students as $key => $item) {
            if ($item['cpf'] == $this->studentData['cpf'] && $item['ra'] == $this->studentData['ra']) {
                $students[$key] = array_merge($item, $this->studentData);
            }
        }

        $this->saveData($students);
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function isStudentDataValid()
    {
        foreach ($this->attributes as $attribute) {
            if (!isset($this->studentData[$attribute]) || !$this->studentData[$attribute]) {
                throw new Exception(sprintf("O atributo '%s' do aluno não foi informado", $attribute));
            }
        }

        return $this->cpfIsValid($this->studentData['cpf']);
    }

    /**
     * @param $cpf
     * @return bool
     */
    public function cpfIsValid($cpf)
    {
        $cpf = preg_replace( '/[^0-9]/is', '', $cpf);

        if (strlen($cpf) != 11 || preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function studentKeyIsValid()
    {
        $studentByCPF = $this->filterStudentBy('cpf', $this->studentData['cpf']);
        if ($studentByCPF && $studentByCPF['ra'] == $this->studentData['ra']) {
            return true;
        }

        $studentByRa = $this->filterStudentBy('ra', $this->studentData['ra']);
        if ($studentByRa && $studentByRa['cpf'] == $this->studentData['cpf']) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function studentExist()
    {
        if (!isset($this->studentData['cpf']) || !$this->studentData['cpf']) {
            throw new Exception("O CPF do aluno não foi informado");
        }

        if (!isset($this->studentData['ra']) || !$this->studentData['ra']) {
            throw new Exception("O RA do aluno não foi informado");
        }

        $studentByCpf = $this->filterStudentBy('cpf', $this->studentData['cpf']);
        $studentByRa = $this->filterStudentBy('ra', $this->studentData['ra']);

        if (!$studentByCpf && !$studentByRa)
            return false;

        return true;
    }

    public function deleteStudent($attribute, $value)
    {
        $students = $this->getAllStudents();
        foreach ($students as $key => $student) {
            if ($student[$attribute] == $value) {
                unset($students[$key]);
            }
        }

        $this->saveData($students);
    }

    /**
     * @param $attribute
     * @param $value
     * @return array|mixed
     * @throws Exception
     */
    public function filterStudentBy($attribute, $value)
    {
        try {
            $data = $this->getAllStudents();
            foreach ($data as $student) {
                if ($student[$attribute] == $value) {
                    return $student;
                }
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        return [];
    }

    /**
     * return all student data from file
     * @return mixed
     */
    public function getAllStudents()
    {
        $data = file_get_contents('db.json');
        return json_decode($data, true);
    }
}