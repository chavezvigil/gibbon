<?php
//THESE EXCEL FUNCTIONS ARE DEPCREATED IN V11 FOR REMOVAL BY V13. They have been replaced by the PHPExcel library in /lib
/*	Author: Raju Mazumder
 	email:rajuniit@gmail.com
	Class:A simple class to export mysql query and whole html and php page to excel,doc etc
 	Downloaded from: http://webscripts.softpedia.com/script/PHP-Clases/Export-To-Excel-50394.html
	License: GNU GPL
*/
/**
 */
namespace Gibbon;

$config = new config();
require_once $config->get('baseDir').'/lib/PHPExcel/Classes/PHPExcel.php';

/**
 * Export to Excel
 *
 * @version	14th April 2016
 * @since	8th April 2016
 */
class Excel extends \PHPExcel
{
	private	$fileName;

	private function setHeader()//this function used to set the header variable
	{
		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename="' . $this->fileName . '"');
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private", false);
	}
	/**
	 * Export with Query
	 *
	 * @version	8th April 2016
	 * @since	8th April 2016
	 * @return	string	Export to Browser.
	 */
	function exportWithQuery($qry, $excel_file_name, $conn)
	{
		$body = NULL ;
		try {
			$tmprst=$conn->query($qry);
			//$tmprst->setFetchMode(PDO::FETCH_NUM);
		}
		catch(PDOException $e) { }

		$this->defineWorkSheet($excel_file_name);
		$this->getProperties()->setTitle("Gibbon Query Dump");
		$this->getProperties()->setSubject("Gibbon Query Dump");
		$this->getProperties()->setDescription("Dump of Query Results generated by Gibbon");

		$header="<center><table cellspacing='0' border=1px>";
		while($row=$tmprst->fetch()) {
			$body.="<tr>";
			while (current($row)) {
				$body.="<td>".$row[key($row)]."</td>";
				next($row);
			}
			$body.="</tr>";
		}

		$this->setHeader($excel_file_name);
		echo $header.$body."</table>";
	}

	/**
	 * define Worksheet
	 *
	 * @version	8th April 2016
	 * @since	8th April 2016
	 * @param	string	File Name
	 * @return	void
	 */
	public function defineWorksheet($fileName)
	{
		$this->getProperties()->setCreator(__(NULL, "Gibbon Edu using PHPExcel"));
		$this->getProperties()->setLastModifiedBy(__(NULL, "Gibbon"));
		$this->getProperties()->setTitle(__(NULL, "Office 2007 XLSX Test Document"));
		$this->getProperties()->setSubject(__(NULL, "Office 2007 XLSX Test Document"));
		$this->getProperties()->setDescription(__(NULL, 'This information is confidential. Generated by Gibbon (https://gibbonedu.org).'));
			$filename = 'No_Name_Set.xlsx';
		$this->fileName = $fileName;
		if (substr($this->fileName, strlen($this->fileName) - 4) === '.xls')
			$this->fileName .= 'x';
	}

	/**
	 * export Worksheet
	 *
	 * @version	9th April 2016
	 * @since	9th April 2016
	 * @param	boolean	Use Excel2007 or >
	 * @return	void
	 */
	public function exportWorksheet($openXML = true)
	{
		// Instantiate a Writer to create an OfficeOpenXML Excel .xlsx file
		// Write the Excel file to filename some_excel_file.xlsx in the current directory
		if ($openXML)
			$objWriter = new \PHPExcel_Writer_Excel2007($this);
		else
		{
			$this->fileName = substr($this->fileName, 0, -1);
			$objWriter = new \PHPExcel_Writer_Excel5($this);
		}
		// Write the Excel file to filename some_excel_file.xlsx in the current directory
		$this->setHeader();

		$objWriter->save('php://output');
		die();
	}

	/**
	 * construct
	 *
	 * @version	9th April 2016
	 * @since	9th April 2016
	 * @param	string	File Name
	 * @return	void
	 */
	public function __construct($fileName = NULL)
	{
		parent::__construct();
		$this->defineWorksheet($fileName);
	}

	/**
	 * cell Colour
	 *
	 * @version	11th April 2016
	 * @since	11th APril 2016
	 * @param	string	Cell/s
	 * @param	string	Colour
	 * @param	object	Chaining
	 */
	public function cellColour($cells, $colour)
	{
    	$this->getActiveSheet()->getStyle($cells)->getFill()->applyFromArray( array(
			'type' => \PHPExcel_Style_Fill::FILL_SOLID,
			'startcolor' => array(
				'rgb' => $colour
			)
		));
		return $this;
	}

	/**
	 * cell Color  (American)
	 *
	 * @version	11th April 2016
	 * @since	11th April 2016
	 * @param	string	Cell/s
	 * @param	string	Colour
	 * @param	object	Chaining
	 */
	public function cellColor($cells, $colour)
	{
		return $this->cellColour($cells, $colour);
	}

	/**
	 * Estimate Cell Count in Spreadsheet
	 *
	 * @version	14th April 2016
	 * @since	14th April 2016
	 * @param	Object	Gibbon\sqlConnection
	 * return	integer	Estimated Cell Count
	 */
	public function estimateCellCount( sqlConnection $pdo)
	{
		if ($pdo->getResult() !== NULL)
			return $pdo->getResult()->columnCount (  ) * $pdo->getResult()->rowCount ( );
		return 0 ;
	}

	/**
	 * cell Font Colour
	 *
	 * @version	14th April 2016
	 * @since	14th APril 2016
	 * @param	string	Cell/s
	 * @param	string	Colour
	 * @param	object	Chaining
	 */
	public function cellFontColour($cells, $colour)
	{
    	$styleArray = array(
    		'font'  => array(
        		'color' => array('rgb' => $colour),
    		)
		);
		$phpExcel->getActiveSheet()->getStyle($cells)->applyFromArray($styleArray);
		return $this;
	}

	/**
	 * cell Font Color  (American)
	 *
	 * @version	14th April 2016
	 * @since	14th April 2016
	 * @param	string	Cell/s
	 * @param	string	Colour
	 * @param	object	Chaining
	 */
	public function cellFontColor($cells, $colour)
	{
		return $this->cellFontColour($cells, $colour);
	}
}
?>
