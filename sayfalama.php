<?php
class sayfalama {

	public $sayfa;
	public $suan;
	public $geri;
	public $ileri;
	public $ileriDonguArtisi;
	public $ileriDonguLimit;
	public $bitis;
	public $baslangic;
	public $veriSayisi;
	public $sayfaVeriLimiti;
	public $geriLimit;
	public $ileriLimit;
	public $arguman;
	public $dosya;
	public $link;
	/* css deðiþken leri tanýmlanýyor */
	public $cssWidthDis = "675px";
	public $cssWidthIc = "650px";

	public $cssBg = "#fff";
	public $cssColor = "#000";
	public $cssFontSize = "11px/15px";
	public $cssFontFamily = "tahoma";
	public $cssBorderColor = "#eee";

	public $cssHoverBg = "#4f9ecd";
	public $cssHoverColor = "#fff";
	public $cssHoverFontSize = "11px/15px";
	public $cssHoverFontFamily = "tahoma";
	public $cssHoverBorderColor = "11px/15px";
	/* css deðiþken tanýmlama iþlemi bitti set iþlemleri baþlýyor */
	function cssWidthIc($string)
	{
		return $this->cssWidthIc = $string;
	}
	function cssWidthDis($string)
	{
		return $this->cssWidthDis = $string;
	}
	function cssBg($string)
	{
		return $this->cssBg = $string;
	}
	function cssColor($string)
	{
		return $this->cssColor = $string;
	}
	function cssFontSize($string)
	{
		return $this->cssFontSize = $string;
	}
	function cssFontFamily($string)
	{
		return $this->cssFontFamily = $string;
	}
	function cssBorderColor($string)
	{
		return $this->cssBorderColor = $string;
	}
	function cssHoverBg($string)
	{
		return $this->cssHoverBg = $string;
	}
	function cssHoverColor($string)
	{
		return $this->cssHoverColor = $string;
	}
	function cssHoverFontSize($string)
	{
		return $this->cssHoverFontSize = $string;
	}
	function cssHoverFontFamily($string)
	{
		return $this->cssHoverFontFamily = $string;
	}
	function cssHoverBorderColor($string)
	{
		return $this->cssHoverBorderColor = $string;
	}
	function css()
	{
		$html='<style type="text/css">
		.sayfalama {
		float:left;
		width:' . $this->cssWidthIc . ';
		padding-top:5px;
		padding-bottom:5px;
		margin-left:5px;
		display:inline;
		}
		.syfm {
		float:left;
		width:' . $this->cssWidthDis . ';
		display:inline;
		padding:3px;
		background:' . $this->cssBg . ';
		color:' . $this->cssColor . ';
		margin-top:5px;
		font:bold ' . $this->cssFontSize . ' "' . $this->cssFontFamily . '";
		}
		.syfm a #seciliSayfa
		{
		background:#cc0000;
		}
		.syfm a{
		height:30px;
		padding:3px;
		background:' . $this->cssBg . ';
		color:' . $this->cssColor . ';
		margin-left:5px;
		text-decoration:none;
		border:1px solid ' . $this->cssBorderColor . ';
		font:bold ' . $this->cssFontSize . ' "' . $this->cssFontFamily . '";
		}
		.syfm a:hover{
		padding:3px;
		background:' . $this->cssHoverBg . ';
		color:' . $this->cssHoverColor . ';
		margin-left:5px;
		text-decoration:none;
		border:1px solid ' . $this->cssHoverBorderColor . ';
		font:bold ' . $this->cssHoverFontSize . ' "' . $this->cssHoverFontFamily . '";
		}
		</style>';
		return $html;
	}
	function sayiDurum($sayi)
	{
		if(substr($sayi,0,1)=='-')
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	function geriLimit($sayi)
	{
		return $this->geriLimit = $sayi;
	}
	function ileriLimit($sayi)
	{
		return $this->ileriLimit = $sayi;
	}
	function veriSayisi($sayi)
	{
		return $this->veriSayisi = intval($sayi);
	}
	function sayfaVeriLimiti($sayi)
	{
		return $this->sayfaVeriLimiti = intval($sayi);
	}
	function suan($sayi)
	{
		return $this->suan = (empty($sayi)) ? 0 : $sayi;
	}
	function argumanlar($string)
	{
		return $this->arguman = $string;
	}
	function dosya($string)
	{
		return $this->dosya = $string;
	}
	function link()
	{
		return $this->link = $this->dosya . "?" . $this->arguman;
	}
	/*
	function obje($obje=null)
	{
	return $this->obje = ($obje == null && $this->obje == null) ? $_GET['obje'] : $obje;
	}
	*/
	function sayfa()
	{
		//try
		//{
		$this->sayfa = ceil($this->veriSayisi / $this->sayfaVeriLimiti);
		//	if(!$this->sayfa)
		//	{
		//		throw new Exception('sýfýra bölünemez');
		//	}
		//}
		//catch (Exception $e) {
		//	echo 'Hata: ',  $e->getMessage(), "\n";
		//}
		return $this->sayfa;
	}
	function geri()
	{
		return $this->geri = $this->suan - 1;
	}
	function ileri()
	{
		return $this->ileri = $this->suan + 1;
	}
	function ileriDonguArtisi()
	{
		$this->ileriDonguArtisi = $this->geriLimit - $this->suan;
		if($this->sayiDurum($this->ileriDonguArtisi))
		{
			return 	$this->ileriDonguLimit = ($this->suan + $this->ileriLimit + $this->ileriDonguArtisi);
		}
		else
		{
			return	$this->ileriDonguLimit = ($this->suan + $this->ileriLimit);
		}
	}
	function bitis()
	{
		for($j=$this->suan;$j<$this->ileriDonguLimit;$j++)
		{
			if($j<$this->sayfa)
			{
				$temp = $j;
			}
			else
			{
				break;
			}
		}
		return	$this->bitis = $temp;
	}
	function baslangic()
	{

		for($k=$this->suan;$k>($this->suan-$this->geriLimit);$k--)
		{
			if($this->sayiDurum($k))
			{
				$temp = $k;
			}
			else
			{
				break;
			}
		}
		return $this->baslangic = $temp;
	}
	function derle()
	{
		$this->sayfa();
		$this->ileri();
		$this->geri();
		$this->ileriDonguArtisi();
		$this->bitis();
		$this->baslangic();
		$this->link();

		$html = null;
		$html.= $this->css();
$html.='<div class="sayfalama"><div class="syfm">';
		$html.='<BR><BR>Sayfalar:';
		// buradaki linkleri - link title ve << , >> iþaretlerinide class dan ayarlaniblir hale getirecez bir ara

		if($this->sayiDurum($this->geri))
		{
		//	$html.="<a href='{$this->link}&sayfa=0' title='Ýlk son sayfaya git'>&laquo;&laquo;</a> ";
			$html.="<a href='{$this->link}&sayfa={$this->geri}' title='Bir önceki sayfaya git'>&laquo;</a> ";
		}
		//echo "basla : {$this->baslangic}<br>Bitiþ {$this->bitis}<hr>";

		for($i=$this->baslangic;$i<=$this->bitis;$i++)
		{
			if($this->suan == $i)
			{
				$html.="<a id='seciliSayfa'><span class=\"sayfa\">".($i+1)."</span></a> ";
			}
			else
			{
				$html.="<a href='{$this->link}&sayfa={$i}' title='".($i+1).".Sayfaya git'>".($i+1)."</a> ";
			}
		}
		if($this->ileri<$this->sayfa)
		{
			$html.="<a href='{$this->link}&sayfa={$this->ileri}' title='Bir sonraki sayfaya git'>&raquo;</a> ";
			//$html.="<a href='{$this->link}&sayfa={$this->sayfa}' title='En son sayfaya git'>&raquo;&raquo;</a> ";
		}
        $html.="</div></div>";
		return $html;
	}
}
?>