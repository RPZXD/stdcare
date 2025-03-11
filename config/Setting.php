<?php
class Setting {
    private $school = "โรงเรียนพิชัย";
    private $titlesystem = "ระบบดูแลช่วยเหลือนักเรียน";
    private $pageTitle;
    private $pageTitleShort = "STDCARE";
    private $logoImage = "/dist/img/logo-phicha.png";

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
}
?>
