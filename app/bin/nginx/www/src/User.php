<?php
class User
{
    private $Email;
    private $AccType;
    private $Password;
    private $FName;
    private $LName;
    private $DOB;
    private $Year;
    private $Rank;
    private $SQuestion;
    private $SAnswer;

    public function __construct()
    {

    }
    
    public function GetEmail()
    {
        return $this->Email;
    }

    public function SetEmail($Email)
    {
        $this->Email = $Email;
    }

    public function GetAccType()
    {
        return $this->AccType;
    }

    public function SetAccType($AccType)
    {
        $this->AccType = $AccType;
    }

    public function GetPassword()
    {
        return $this->Password;
    }

    public function SetPassword($Password)
    {
        $this->Password = $Password;
    }

    public function GetFName()
    {
        return $this->FName;
    }

    public function SetFName($FName)
    {
        $this->FName = $FName;
    }

    public function GetLName()
    {
        return $this->LName;
    }

    public function SetLName($LName)
    {
        $this->LName = $LName;
    }

    public function GetDOB()
    {
        return $this->DOB;
    }

    public function SetDOB($DOB)
    {
        $this->DOB = $DOB;
    }

    public function GetYear()
    {
        return $this->Year;
    }

    public function SetYear($Year)
    {
        $this->Year = $Year;
    }

    public function GetRank()
    {
        return $this->Rank;
    }

    public function SetRank($Rank)
    {
        $this->Rank = $Rank;
    }

    public function GetSQuestion()
    {
        return $this->SQuestion;
    }

    public function SetSQuestion($SQuestion)
    {
        $this->SQuestion = $SQuestion;
    }

    public function GetSAnswer()
    {
        return $this->SAnswer;
    }

    public function SetSAnswer($SAnswer)
    {
        $this->SAnswer = $SAnswer;
    }
}