<?php
class Setting {
    private $school = "โรงเรียนพิชัย";
    private $titlesystem = "ระบบดูแลช่วยเหลือนักเรียน";
    private $pageTitle;
    private $pageTitleShort = "STDCARE";
    private $logoImage = "/stdcare/dist/img/logo-phicha.png";
    private $imgProfile = "/stdcare/teacher/uploads/phototeach/";
    private $imgVisitHome = "/stdcare/teacher/uploads/visithome/";
    private $imgProfileStudent = "/stdcare/photo/";
    private $imgAwards = "/stdcare/teacher/uploads/file_award/";
    private $imgTraining = "/stdcare/teacher/uploads/file_seminar/";
    private $uploadDir_seminar = "../uploads/file_seminar/";
    private $uploadDir_award = "../uploads/file_award/";
    private $uploadDir_profile = "/teacher/uploads/phototeach/";

    public function getPageTitle() {
        $this->pageTitle = $this->titlesystem . " | " . $this->school;
        return $this->pageTitle;
    }

    public function getPageTitleShort() {
        return $this->pageTitleShort;
    }

    public function getLogoImage() {
        return $this->logoImage;
    }

    public function getImgProfile() {
        return $this->imgProfile;
    }

    public function getImgVisitHome() {
        return $this->imgVisitHome;
    }

    public function getImgProfileStudent() {
        return $this->imgProfileStudent;
    }

    public function getImgAwards() {
        return $this->imgAwards;
    }
    public function getImgTraining() {
        return $this->imgTraining;
    }
    public function getUploadDir_seminar() {
        return $this->uploadDir_seminar;
    }
    public function getUploadDir_award() {
        return $this->uploadDir_award;
    }
    public function getUploadDir_profile() {
        return $this->uploadDir_profile;
    }
}
?>
