<?php
/////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////
//// SISFO-TOKO v2.1                                 ////
/////////////////////////////////////////////////////////
//// Dibuat Oleh :                                   ////
////    Agus Muhajir, S.Kom                          ////
/////////////////////////////////////////////////////////
//// URL    : http://hajirodeon.wordpress.com/       ////
//// E-Mail : hajirodeon@yahoo.com                   ////
//// HP/SMS : 081-829-88-54                          ////
/////////////////////////////////////////////////////////
//// Milist :                                        ////
////    http://yahoogroup.com/groups/linuxbiasawae/  ////
////    http://yahoogroup.com/groups/sisfokol/       ////
/////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////


session_start();

require("../../inc/config.php");
require("../../inc/fungsi.php");
require("../../inc/koneksi.php");
require("../../inc/cek/adm.php");
require("../../inc/class/paging.php");
$tpl = LoadTpl("../../template/index.html");

nocache;

//nilai
$filenya = "lap_history_jual.php";
$judul = "Laporan History Penjualan";
$judulku = "[$admin_session : $username1_session] ==> $judul";
$judulx = $judul;
$xbln1 = nosql($_REQUEST['xbln1']);
$xthn1 = nosql($_REQUEST['xthn1']);
$brgkd = nosql($_REQUEST['brgkd']);
$page = nosql($_REQUEST['page']);
if ((empty($page)) OR ($page == "0"))
	{
	$page = "1";
	}


//focus
//nek sih null
if (empty($xbln1))
	{
	$diload = "document.formx.xbln1.focus();";
	}
else if (empty($xthn1))
	{
	$diload = "document.formx.xthn1.focus();";
	}
else if (empty($brgkd))
	{
	$diload = "document.formx.kode.focus();";
	}



//PROSES ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//nek ok
if ($_POST['btnOK'])
	{
	$xbln1 = nosql($_POST['xbln1']);
	$xthn1 = nosql($_POST['xthn1']);
	$kode = nosql($_POST['kode']);


	//cek
	$qcc = mysql_query("SELECT * FROM m_brg ".
							"WHERE kode = '$kode'");
	$rcc = mysql_fetch_assoc($qcc);
	$tcc = mysql_num_rows($qcc);
	$cc_kd = nosql($rcc['kd']);


	//nek ada, benar
	if ($tcc != 0)
		{
		$ke = "$filenya?xbln1=$xbln1&xthn1=$xthn1&brgkd=$cc_kd";
		xloc($ke);
		exit();
		}
	}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////





//isi *START
ob_start();


//query
$p = new Pager();
$start = $p->findStart($limit);

$sqlcount = "SELECT jual.*, jual_detail.*, m_brg.*, m_brg.nama AS mbnm, ".
				"m_satuan.*, m_kastumer.*, m_kastumer.singkatan AS kastumer ".
				"FROM jual, jual_detail, m_brg, m_satuan, m_kastumer ".
				"WHERE jual.kd = jual_detail.kd_jual ".
				"AND jual_detail.kd_brg = m_brg.kd ".
				"AND m_brg.kd_satuan = m_satuan.kd ".
				"AND jual.kd_kastumer = m_kastumer.kd ".
				"AND m_brg.kd = '$brgkd' ".
				"AND round(DATE_FORMAT(jual.tgl_jual, '%m')) = '$xbln1' ".
				"AND round(DATE_FORMAT(jual.tgl_jual, '%Y')) = '$xthn1' ".
				"ORDER BY jual.tgl_jual ASC";

$sqlresult = $sqlcount;

$count = mysql_num_rows(mysql_query($sqlcount));
$pages = $p->findPages($count, $limit);
$result = mysql_query("$sqlresult LIMIT ".$start.", ".$limit);
$target = "$filenya?brgkd=$brgkd&xbln1=$xbln1&xthn1=$xthn1";
$pagelist = $p->pageList($_GET['page'], $pages, $target);
$data = mysql_fetch_array($result);

//nilai data
$brg_kode = nosql($data['kode']);
$brg_nm = balikin($data['mbnm']);


//window
echo '
<script type="text/javascript" src="'.$sumber.'/inc/js/dhtmlwindow.js"></script>
<script type="text/javascript" src="'.$sumber.'/inc/js/modal.js"></script>
<script type="text/javascript">

function open_brg()
	{
	brg_window=dhtmlmodal.open(\'Barang\',
	\'iframe\',
	\'popup_brg.php\',
	\'Barang\',
	\'width=700px,height=325px,center=1,resize=0,scrolling=0\')

	brg_window.onclose=function()
		{
		var kodex=this.contentDoc.getElementById("kodex");

		document.formx.kode.value=kodex.value;
		document.formx.kode.focus();
		return true
		}
	}
</script>';


//require
require("../../inc/js/jumpmenu.js");
require("../../inc/js/swap.js");
require("../../inc/js/listmenu.js");
require("../../inc/menu/adm.php");
require("../../inc/menu/adm_cek.php");


//view //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
echo '<form method="post" action="'.$filenya.'" name="formx">
<table width="100%" border="0" cellspacing="0" cellpadding="3">
<tr>
<td>';
xheadline($judul);
echo '</td>
</tr>
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="3">
<tr bgcolor="'.$warna02.'">
<td>
<strong>Bulan : </strong>';
echo "<select name=\"xbln1\" onChange=\"MM_jumpMenu('self',this,0)\">";
echo '<option value="'.$xbln1.'" selected>'.$arrbln[$xbln1].'</option>';

for ($j=1;$j<=12;$j++)
	{
	echo '<option value="'.$filenya.'?xbln1='.$j.'">'.$arrbln[$j].'</option>';
	}

echo '</select>';

echo "<select name=\"xthn1\" onChange=\"MM_jumpMenu('self',this,0)\">";
echo '<option value="'.$xthn1.'" selected>'.$xthn1.'</option>';

//query
$qthn = mysql_query("SELECT * FROM m_tahun ".
						"ORDER BY tahun DESC");
$rthn = mysql_fetch_assoc($qthn);

do
	{
	$x_thn = nosql($rthn['tahun']);
	echo '<option value="'.$filenya.'?xbln1='.$xbln1.'&xthn1='.$x_thn.'">'.$x_thn.'</option>';
	}
while ($rthn = mysql_fetch_assoc($qthn));

echo '</select>,
<strong>Kode Barang : </strong>
<input name="kode" type="text" value="'.$brg_kode.'" size="15"
onKeyDown="var keyCode = event.keyCode;
if (keyCode == 13)
	{
	document.formx.btnOK.focus();
	document.formx.btnOK.submit();
	}

if (keyCode == 45)
	{
	open_brg();
	return false
	}
">
<input name="btnOK" type="submit" value=">>">
[INSERT : Pilih Kode Barang].
</td>
</tr>
</table>
<br>';


//cek
if ((empty($xbln1)) OR (empty($xthn1)))
	{
	echo '<strong>Per Bulan Apa...?</strong>';
	}
else if (empty($brgkd))
	{
	echo '<strong>Kode Barang Masih Kosong...!!</strong>';
	}
else
	{
	if ($count != 0)
		{
		echo 'Kode Barang : <strong>'.$brg_kode.'</strong>,
		Nama Barang : <strong>'.$brg_nm.'</strong>';
		echo '<table width="700" border="1" cellspacing="0" cellpadding="3">
		<tr valign="top" bgcolor="'.$warnaheader.'">
		<td width="100"><strong><font color="'.$warnatext.'">Tanggal</font></strong></td>
		<td width="100"><strong><font color="'.$warnatext.'">No. Faktur</font></strong></td>
		<td><strong><font color="'.$warnatext.'">Kastumer</font></strong></td>
		<td width="100"><strong><font color="'.$warnatext.'">Jumlah</font></strong></td>
		</tr>';

		do
			{
			if ($warna_set ==0)
				{
				$warna = $warna01;
				$warna_set = 1;
				}
			else
				{
				$warna = $warna02;
				$warna_set = 0;
				}

			$nomer = $nomer + 1;
			$y_kd = nosql($data['kd']);
			$y_tgl_jual = $data['tgl_jual'];
			$y_no_faktur = balikin($data['no_faktur']);
			$y_kastumer = balikin($data['kastumer']);
			$y_qty = nosql($data['qty']);
			$y_satuan = balikin($data['satuan']);



			echo "<tr valign=\"top\" bgcolor=\"$warna\" onmouseover=\"this.bgColor='$warnaover';\" onmouseout=\"this.bgColor='$warna';\">";
			echo '<td>'.$y_tgl_jual.'</td>
			<td>'.$y_no_faktur.'</td>
			<td>'.$y_kastumer.'</td>
			<td>'.$y_qty.' '.$y_satuan.'</td>
	        </tr>';
			}
		while ($data = mysql_fetch_assoc($result));

		echo '</table>

		<table width="700" border="0" cellspacing="0" cellpadding="3">
		<tr>
		<td>
		[<a href="lap_history_jual_prt.php?brgkd='.$brgkd.'&xbln1='.$xbln1.'&xthn1='.$xthn1.'" title="PRINT...!!"><img src="../../img/print.gif" border="0"></a>]
		</td>
		<td align="right"><strong><font color="#FF0000">'.$count.'</font></strong> Data. '.$pagelist.'</td>
		</tr>
		</table>';
		}
	else
		{
		echo '<font color="red"><strong>TIDAK ADA HISTORY.</strong></font>';
		}
	}

echo '</form>';

//isi
$isi = ob_get_contents();
ob_end_clean();

require("../../inc/niltpl.php");

//null-kan
xclose($koneksi);
exit();
?>