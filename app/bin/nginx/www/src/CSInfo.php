<?php
/*
CSInfo - Course search info
Used to hold search query data from the course search form
*/
class CSInfo
{
    private $CourseId;
    private $CourseName;
    private $Semester;
    private $Department;

    public function __construct()
    {

    }
    
    public function GetCourseId()
    {
        return $this->CourseId;
    }

    public function SetCourseId($CourseId)
    {
        $this->CourseId = $CourseId;
    }

    public function GetCourseName()
    {
        return $this->CourseName;
    }

    public function SetCourseName($CourseName)
    {
        $this->CourseName = $CourseName;
    }

    public function GetSemester()
    {
        return $this->Semester;
    }

    public function SetSemester($Semester)
    {
        $this->Semester = $Semester;
    }

    public function GetDepartment()
    {
        return $this->Department;
    }

    public function SetDepartment($Department)
    {
        $this->Department = $Department;
    }
}