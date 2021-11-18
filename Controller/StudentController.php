<?php

/**
 * @author Israel dos Santos Elias
 * @author Janaina Ferreira da Silva
 * @author Juliana Nascimento Silva
 */
class StudentController extends AbstractController
{
    /**
     * Return a list with all students
     */
    public function list()
    {
        $statusHeader = "HTTP/1.1 200 OK";
        $error = false;

        if (strtoupper($_SERVER['REQUEST_METHOD']) == 'GET') {
            try {
                /** @var Student $student */
                $student = new Student();
                $responseData = json_encode($student->getAllStudents());
            } catch (Exception $e) {
                $responseData = $e->getMessage() . 'Something went wrong! Please contact support.';
                $statusHeader = 'HTTP/1.1 500 Internal Server Error';
                $error = true;
            }
        } else {
            $responseData = 'Method not supported';
            $statusHeader = 'HTTP/1.1 405 Method Not Allowed';
            $error = true;
        }

        $this->setResponse($statusHeader, $responseData, $error);
    }

    /**
     * Return a specific student filtered by cpf or ra
     */
    public function student()
    {
        $error = true;
        $errorCode = "500 Internal Server Error";

        if (strtoupper($_SERVER['REQUEST_METHOD']) == 'GET') {
            try {
                $params = $this->getQueryStringParams();

                if (
                    (!isset($params['cpf']) || !$params['cpf'])
                    && (!isset($params['ra']) || !$params['ra'])
                ) {
                    $errorCode = "400 Bad Request";
                    throw new Exception('Por favor, informe o CPF ou RA do aluno');
                }

                $attributeToFilter = isset($params['cpf']) && $params['cpf'] ? 'cpf' : 'ra';
                $valueToFilter = $params[$attributeToFilter];

                /** @var Student $student */
                $student = new Student();
                $responseData = json_encode($student->filterStudentBy($attributeToFilter, $valueToFilter));

                $error = false;
                $statusHeader = "HTTP/1.1 200 OK";
            } catch (Exception $e) {
                $responseData = $e->getMessage() ?? 'Houve um erro ao processar sua requisição';
                $statusHeader = "HTTP/1.1 {$errorCode}";
            }
        } else {
            $responseData = 'Method not supported';
            $statusHeader = 'HTTP/1.1 405 Method Not Allowed';
        }

        $this->setResponse($statusHeader, $responseData, $error);
    }

    /**
     * Create a student
     */
    public function createStudent()
    {
        $error = true;
        $errorCode = "500 Internal Server Error";

        if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
            try {
                $params = json_decode($this->getRawData(), true);

                /** @var Student $student */
                $student = new Student();
                $student->setData($params);

                if (!$student->isStudentDataValid()) {
                    $errorCode = "400 Bad Request";
                    throw new Exception('CPF invalido');
                }

                if ($student->studentExist()) {
                    $errorCode = "400 Bad Request";
                    throw new Exception('Os dados informados já pertencem a outro estudante');
                }

                $student->saveNewData();
                $responseData = 'Estudante criado';
                $error = false;
                $statusHeader = "HTTP/1.1 201 Created";
            } catch (Exception $e) {
                $responseData = $e->getMessage() ?? 'Houve um erro ao processar sua requisição';
                $statusHeader = "HTTP/1.1 {$errorCode}";
            }
        } else {
            $responseData = 'Method not supported';
            $statusHeader = 'HTTP/1.1 405 Method Not Allowed';
        }

        $this->setResponse($statusHeader, $responseData, $error);
    }

    /**
     * Update student information
     */
    public function updateStudent()
    {
        $error = true;
        $errorCode = "500 Internal Server Error";

        if (strtoupper($_SERVER['REQUEST_METHOD']) == 'PUT') {
            try {
                $params = json_decode($this->getRawData(), true);

                /** @var Student $student */
                $student = new Student();
                $student->setData($params);

                if (!$student->studentExist() && $student->studentKeyIsValid()) {
                    $errorCode = "400 Bad Request";
                    throw new Exception('Os dados informados estão incorretos, confira o CPF e RA informado');
                }

                $student->updateData();
                $responseData = 'Estudante atualizado';
                $error = false;
                $statusHeader = "HTTP/1.1 200 OK";
            } catch (Exception $e) {
                $responseData = $e->getMessage() ?? 'Houve um erro ao processar sua requisição';
                $statusHeader = "HTTP/1.1 {$errorCode}";
            }
        } else {
            $responseData = 'Method not supported';
            $statusHeader = 'HTTP/1.1 405 Method Not Allowed';
        }

        $this->setResponse($statusHeader, $responseData, $error);
    }

    /**
     * Delete student information
     */
    public function deleteStudent()
    {
        $error = true;
        $errorCode = "500 Internal Server Error";

        if (strtoupper($_SERVER['REQUEST_METHOD']) == 'DELETE') {
            try {
                $params = $this->getQueryStringParams();

                if (
                    (!isset($params['cpf']) || !$params['cpf'])
                    && (!isset($params['ra']) || !$params['ra'])
                ) {
                    $errorCode = "400 Bad Request";
                    throw new Exception('Por favor, informe o CPF ou RA do aluno que será removido');
                }

                /** @var Student $student */
                $student = new Student();

                $attributeToFilter = isset($params['cpf']) && $params['cpf'] ? 'cpf' : 'ra';
                $valueToFilter = $params[$attributeToFilter];

                if (!$student->filterStudentBy($attributeToFilter, $valueToFilter)) {
                    $errorCode = "400 Bad Request";
                    throw new Exception('Os dados informados não pertencem a nenhum estudante');
                }

                $student->deleteStudent($attributeToFilter, $valueToFilter);

                $responseData = 'Estudante excluido';
                $error = false;
                $statusHeader = "HTTP/1.1 200 OK";
            } catch (Exception $e) {
                $responseData = $e->getMessage() ?? 'Houve um erro ao processar sua requisição';
                $statusHeader = "HTTP/1.1 {$errorCode}";
            }
        } else {
            $responseData = 'Method not supported';
            $statusHeader = 'HTTP/1.1 405 Method Not Allowed';
        }

        $this->setResponse($statusHeader, $responseData, $error);
    }

    /**
     * @param $statusHeader
     * @param $response
     * @param false $error
     */
    protected function setResponse($statusHeader, $response, $error = false)
    {
        if ($error) {
            $response = json_encode(['error' => $response]);
        }

        $this->sendOutput(
            $response,
            array('Content-Type: application/json', $statusHeader)
        );
    }
}