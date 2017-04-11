# Sayfalamacı, yıllar yıllar önce yazılmış bir sınıf. Sayfa boş durmaması amacıyla yüklenmiştir. 

Örnek kullanım:

<?php
include_once "sayfalama.php";
        $sayfalama = new sayfalama();
        $sayfalama->geriLimit(5);
        $sayfalama->ileriLimit(5);
        $sayfalama->veriSayisi($veriSayisi['toplam']);//VT'de toplam ne kadar veri var?
        $sayfalama->sayfaVeriLimiti($sayfaLimiti); //Bir sayfada kaç sonuç gözükecek
        $sayfalama->suan($_GET['sayfa']);//şuanki mevcut sayfa
        $sayfalama->argumanlar("bolum={$_GET['bolum']}&obje={$_GET['obje']}");
        $sayfalama->dosya("index.php");
        $sayfaHtml = $sayfalama->derle();
?>