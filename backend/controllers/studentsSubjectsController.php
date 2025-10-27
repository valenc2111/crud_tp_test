<?php
/**
*    File        : backend/controllers/studentsSubjectsController.php
*    Project     : CRUD PHP
*    Author      : Tecnologías Informáticas B - Facultad de Ingeniería - UNMdP
*    License     : http://www.gnu.org/licenses/gpl.txt  GNU GPL 3.0
*    Date        : Mayo 2025
*    Status      : Prototype
*    Iteration   : 1.0 ( prototype )
*/

require_once("./repositories/studentsSubjects.php");

function handleGet($conn) 
{
   if (isset($_GET['id'])) 
    {
        $studentsSubjects = getAllSubjectsStudents($conn);
        echo json_encode($studentsSubjects);
    }
        //2.1
    else if (isset($_GET['page']) && isset($_GET['limit'])) 
    {
        $page = (int)$_GET['page'];
        $limit = (int)$_GET['limit'];
        $offset = ($page - 1) * $limit;

        $studentsSubjects = getPaginatedStudentsSubjects($conn, $limit, $offset);
        $total = getTotalStudentsSubjects($conn);

        echo json_encode([
            'students_subjects' => $studentsSubjects, // ya es array
            'total' => $total        // ya es entero
        ]);
    }
    else
    {
        $studentsSubjects = getAllStudentSubjects($conn); // ya es array
        echo json_encode($studentsSubjects);
    }
}

function handlePost($conn) //3.0 new
{
    $input = json_decode(file_get_contents("php://input"), true);

    try {
        $result = createSubject($conn, $input['name']);
        if ($result['inserted'] > 0) {
            echo json_encode(["message" => "Materia creada correctamente"]);
        } else {
            http_response_code(400);
            echo json_encode(["error" => "No se pudo crear la materia"]);
        }
    } catch (mysqli_sql_exception $e) {
        // 🔹 Código 1062 = duplicado
        if ($e->getCode() == 1062) {
            http_response_code(400);
            echo json_encode(["error" => "La materia ya existe."]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error interno del servidor"]);
        }
    }
}


/*function handlePost($conn) //old 
{
    $input = json_decode(file_get_contents("php://input"), true);
    
    $result = assignSubjectToStudent($conn, $input['student_id'], $input['subject_id'], $input['approved']);
    if ($result['inserted'] > 0) 
    {
        echo json_encode(["message" => "Asignación realizada"]);
    } 
    else 
    {
        http_response_code(500);
        echo json_encode(["error" => "Error al asignar"]);
    }
}*/

function handlePut($conn) 
{
    $input = json_decode(file_get_contents("php://input"), true);

    if (!isset($input['id'], $input['student_id'], $input['subject_id'], $input['approved'])) 
    {
        http_response_code(400);
        echo json_encode(["error" => "Datos incompletos"]);
        return;
    }

    $result = updateStudentSubject($conn, $input['id'], $input['student_id'], $input['subject_id'], $input['approved']);
    if ($result['updated'] > 0) 
    {
        echo json_encode(["message" => "Actualización correcta"]);
    } 
    else 
    {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo actualizar"]);
    }
}

function handleDelete($conn) 
{
    $input = json_decode(file_get_contents("php://input"), true);

    $result = removeStudentSubject($conn, $input['id']);
    if ($result['deleted'] > 0) 
    {
        echo json_encode(["message" => "Relación eliminada"]);
    } 
    else 
    {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo eliminar"]);
    }
}
?>