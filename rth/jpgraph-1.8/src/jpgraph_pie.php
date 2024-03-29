<?php
/*=======================================================================
// File:	JPGRAPH_PIE.PHP
// Description:	Pie plot extension for JpGraph
// Created: 	2001-02-14
// Author:	Johan Persson (johanp@aditus.nu)
// Ver:		$Id: jpgraph_pie.php,v 1.1.1.1 2005/11/30 23:01:57 gth2 Exp $
//
// License:	This code is released under QPL
// Copyright (C) 2001,2002 Johan Persson
//========================================================================
*/


// Defines for PiePlot::SetLabelType()
DEFINE("PIE_VALUE_ABS",1);
DEFINE("PIE_VALUE_PER",0);
DEFINE("PIE_VALUE_PERCENTAGE",0);

//===================================================
// CLASS PiePlot
// Description: Draws a pie plot
//===================================================
class PiePlot {
    var $posx=0.3,$posy=0.5;
    var $radius=0.3;
    var $explode_radius=array(),$explode_all=false,$explode_r=20;
    var $labels, $legends=null;
    var $csimtargets=null;  // Array of targets for CSIM
    var $csimareas='';		// Generated CSIM text	
    var $csimalts=null;		// ALT tags for corresponding target
    var $data=null;
    var $title;
    var $startangle=0;
    var $weight=1, $color="black";
    var $legend_margin=6,$show_labels=true;
    var $themearr = array(
	"earth" 	=> array(136,34,40,45,46,62,63,134,74,10,120,136,141,168,180,77,209,218,346,395,89,430),
	"pastel" => array(27,415,128,59,66,79,105,110,42,147,152,230,236,240,331,337,405,38),
	"water"  => array(8,370,24,40,335,56,213,237,268,14,326,387,10,388),
	"test"   => array(390,28,350,340,130,15,58),
	"pca"   => array(390,28,350,6,340,89,15,58),
	"sand"   => array(27,168,34,170,19,50,65,72,131,209,46,393));
    var $theme="earth";
    var $setslicecolors=array();
    var $labeltype=0; // Default to percentage
    var $pie_border=true,$pie_interior_border=true;
    var $value;
    var $ishadowcolor='',$ishadowdrop=4;
    var $ilabelposadj=1;

	
//---------------
// CONSTRUCTOR
    function PiePlot(&$data) {
	$this->data = $data;
	$this->title = new Text("");
	$this->title->SetFont(FF_FONT1,FS_BOLD);
	$this->value = new DisplayValue();
	$this->value->Show();
	$this->value->SetFormat('%.1f%%');
    }

//---------------
// PUBLIC METHODS	
    function SetCenter($x,$y=0.5) {
	$this->posx = $x;
	$this->posy = $y;
    }

    function SetColor($aColor) {
	$this->color = $aColor;
    }

    function SetShadow($aColor='darkgray',$aDropWidth=4) {
	$this->ishadowcolor = $aColor;
	$this->ishadowdrop = $aDropWidth;
    }

    function SetCSIMTargets(&$targets,$alts=null) {
	$this->csimtargets=$targets;
	$this->csimalts=$alts;
    }
	
    function GetCSIMareas() {
	return $this->csimareas;
    }

    function AddSliceToCSIM($i,$xc,$yc,$radius,$sa,$ea) {  
        //Slice number, ellipse centre (x,y), height, width, start angle, end angle
	while( $sa > 2*M_PI ) $sa = $sa - 2*M_PI;
	while( $ea > 2*M_PI ) $ea = $ea - 2*M_PI;

	$sa = 2*M_PI - $sa;
	$ea = 2*M_PI - $ea;

	//add coordinates of the centre to the map
	$coords = "$xc, $yc";

	//add coordinates of the first point on the arc to the map
	$xp = floor(($radius*cos($ea))+$xc);
	$yp = floor($yc-$radius*sin($ea));
	$coords.= ", $xp, $yp";
	
	//add coordinates every 0.2 radians
	$a=$ea+0.2;
	while ($a<$sa) {
	    $xp = floor($radius*cos($a)+$xc);
	    $yp = floor($yc-$radius*sin($a));
	    $coords.= ", $xp, $yp";
	    $a += 0.2;
	}
		
	//Add the last point on the arc
	$xp = floor($radius*cos($sa)+$xc);
	$yp = floor($yc-$radius*sin($sa));
	$coords.= ", $xp, $yp";
	if( !empty($this->csimtargets[$i]) )
	    $this->csimareas .= "<area shape=\"poly\" coords=\"$coords\" href=\"".
		$this->csimtargets[$i]."\"";
	if( !empty($this->csimalts[$i]) ) {
	    $tmp=sprintf($this->csimalts[$i],$this->data[$i]);
	    $this->csimareas .= " alt=\"$tmp\" title=\"$tmp\"";
	}
	$this->csimareas .= ">\n";
    }

	
    function SetTheme($aTheme) {
	if( in_array($aTheme,array_keys($this->themearr)) )
	    $this->theme = $aTheme;
	else
	    JpGraphError::Raise("PiePLot::SetTheme() Unknown theme: $aTheme");
    }
	
    function ExplodeSlice($e,$radius=20) {
	$this->explode_radius[$e]=$radius;
    }

    function ExplodeAll($radius=20) {
	$this->explode_all=true;
	$this->explode_r = $radius;
    }

    function Explode($aExplodeArr) {
	if( !is_array($aExplodeArr) ) {
	    JpGraphError::Raise("Argument to PiePlot::Explode() must be an array.");
	}
	$this->explode_radius = $aExplodeArr;
    }
	
    function SetSliceColors($aColors) {
	$this->setslicecolors = $aColors;
    }
	
    function SetStartAngle($aStart) {
	$this->startangle = $aStart;
    }
	
    function SetFont($family,$style=FS_NORMAL,$size=10) {
		JpGraphError::Raise('PiePlot::SetFont() is deprecated. Use PiePlot->value->SetFont() instead.');
    }
	
    // Size in percentage
    function SetSize($aSize) {
	if( ($aSize>0 && $aSize<=0.5) || ($aSize>10 && $aSize<1000) )
	    $this->radius = $aSize;
	else
	    JpGraphError::Raise("PiePlot::SetSize() Radius for pie must either be specified as a fraction
                                [0, 0.5] of the size of the image or as an absolute size in pixels 
                                in the range [10, 1000]");
    }
	
    function SetFontColor($aColor) {
	JpGraphError::Raise('PiePlot::SetFontColor() is deprecated. Use PiePlot->value->SetColor() instead.');
    }
	
    // Set label arrays
    function SetLegends($aLegend) {
	$this->legends = $aLegend;
    }

    // Set text labels for slices 
    function SetLabels($aLabels,$aLblPosAdj="auto") {
	$this->labels = $aLabels;
	$this->ilabelposadj=$aLblPosAdj;
    }

    function SetLabelPos($aLblPosAdj) {
	$this->ilabelposadj=$aLblPosAdj;
    }
	
    // Should we display actual value or percentage?
    function SetLabelType($t) {
	if( $t<0 || $t>1 ) 
	    JpGraphError::Raise("PiePlot::SetLabelType() Type for pie plots must be 0 or 1 (not $t).");
	$this->labeltype=$t;
    }

    function SetValueType($aType) {
	$this->SetLabelType($aType);
    }


    // Should the circle around a pie plot be displayed
    function ShowBorder($exterior=true,$interior=true) {
	$this->pie_border = $exterior;
	$this->pie_interior_border = $interior;
    }
	
    // Setup the legends
    function Legend(&$graph) {
	$colors = array_keys($graph->img->rgb->rgb_table);
   	sort($colors);	
   	$ta=$this->themearr[$this->theme];	
   	
   	if( $this->setslicecolors==null ) 
	    $numcolors=count($ta);
   	else
	    $numcolors=count($this->setslicecolors);
		
	$sum=0;
	for($i=0; $i<count($this->data); ++$i)
	    $sum += $this->data[$i];

	// Bail out with error if the sum is 0
	if( $sum==0 )
	    JpGraphError::Raise("Illegal pie plot. Sum of all data is zero for Pie!");

	$i=0;
	if( count($this->legends)>0 ) {
	    foreach( $this->legends as $l ) {
				
		// Replace possible format with actual values
		if( $this->labeltype==0 )
		    $l = sprintf($l,100*$this->data[$i]/$sum);
		else
		    $l = sprintf($l,$this->data[$i]);
				
		if( $this->setslicecolors==null ) 
		    $graph->legend->Add($l,$colors[$ta[$i%$numcolors]]);
		else
		    $graph->legend->Add($l,$this->setslicecolors[$i%$numcolors]);
		++$i;
				
				// Breakout if there are more legends then values
		if( $i==count($this->data) ) return;
	    }
	}
    }
	
    function Stroke(&$img) {
		
	$colors = array_keys($img->rgb->rgb_table);
   	sort($colors);	
   	$ta=$this->themearr[$this->theme];	
   	
   	if( $this->setslicecolors==null ) 
	    $numcolors=count($ta);
   	else
	    $numcolors=count($this->setslicecolors);
   	
	// Draw the slices
	$sum=0;
	$n = count($this->data);
	for($i=0; $i < $n; ++$i)
	    $sum += $this->data[$i];
	
	// Bail out with error if the sum is 0
	if( $sum==0 )
	    JpGraphError::Raise("Sum of all data is 0 for Pie.");
	
	// Format the titles for each slice
	for( $i=0; $i<count($this->data); ++$i) {
	    if( $this->labeltype==0 )
		if( $sum != 0 )
		    $l = 100.0*$this->data[$i]/$sum;
		else
		    $l = 0.0;
	    else
		$l = $this->data[$i]*1.0;
	    if( isset($this->labels[$i]) && is_string($this->labels[$i]) )
		$this->labels[$i]=sprintf($this->labels[$i],$l);
	    else
		$this->labels[$i]=$l;
	}
		
	// Set up the pie-circle
	if( $this->radius < 1 )
	    $this->radius = floor($this->radius*min($img->width,$img->height));
	else
	    $this->radius = $this->radius;
	$xc = round($this->posx*$img->width);
	$yc = round($this->posy*$img->height);
		
	$this->startangle = $this->startangle*M_PI/180;

	if( $this->explode_all )
	    for($i=0;$i<count($this->data);++$i)
		$this->explode_radius[$i]=$this->explode_r;

	$n = count($this->data);

	if( $this->ishadowcolor != "") {
	    $accsum=0;
	    $angle2 = $this->startangle;
	    $img->SetColor($this->ishadowcolor);
	    for($i=0; $sum>0 && $i < $n; ++$i) {
		$d = $this->data[$i];
		$angle1 = $angle2;
		$accsum += $d;
		$angle2 = $this->startangle+2*M_PI*$accsum/$sum;
		if( empty($this->explode_radius[$i]) )
		    $this->explode_radius[$i]=0;

		$la = 2*M_PI - (abs($angle2-$angle1)/2.0+$angle1);
		$xcm = $xc + $this->explode_radius[$i]*cos($la);
		$ycm = $yc - $this->explode_radius[$i]*sin($la);
		
		$xcm += $this->ishadowdrop;
		$ycm += $this->ishadowdrop;

		$img->CakeSlice($xcm,$ycm,$this->radius,$this->radius,
				$angle1*180/M_PI,$angle2*180/M_PI,$this->ishadowcolor);
		
	    }
	}

	$accsum=0;
	$angle2 = $this->startangle;
	$img->SetColor($this->color);

	for($i=0; $sum>0 && $i < $n; ++$i) {
	    $d = $this->data[$i];
	    $angle1 = $angle2;
	    $accsum += $d;
	    $angle2 = $this->startangle+2*M_PI*$accsum/$sum;
	    
	    if( $this->setslicecolors==null )
		$slicecolor=$colors[$ta[$i%$numcolors]];
	    else
		$slicecolor=$this->setslicecolors[$i%$numcolors];

	    if( $this->pie_interior_border )
		$img->SetColor($this->color);
	    else
		$img->SetColor($slicecolor);

	    $arccolor = $this->pie_border ? $this->color : "";

	    $this->la[$i] = 2*M_PI - (abs($angle2-$angle1)/2.0+$angle1);

	    if( empty($this->explode_radius[$i]) )
		$this->explode_radius[$i]=0;

	    $xcm = $xc + $this->explode_radius[$i]*cos($this->la[$i]);
	    $ycm = $yc - $this->explode_radius[$i]*sin($this->la[$i]);
	    
	    $img->CakeSlice($xcm,$ycm,$this->radius-1,$this->radius-1,
			    $angle1*180/M_PI,$angle2*180/M_PI,$slicecolor,$arccolor);

	    if ($this->csimtargets) 
		$this->AddSliceToCSIM($i,$xcm,$ycm,$this->radius,$angle1,$angle2);

	}

	if( $this->value->show ) 
	    $this->StrokeAllLabels($img,$xc,$yc);

	// Adjust title position
	$this->title->Pos($xc,
			  $yc-$this->title->GetFontHeight($img)-$this->radius-$this->title->margin,
			  "center","bottom");
	$this->title->Stroke($img);
		
    }

//---------------
// PRIVATE METHODS	

    function StrokeAllLabels($img,$xc,$yc) {
	$n = count($this->data);
	for($i=0; $i < $n; ++$i) {
	    $this->StrokeLabel($this->labels[$i],$img,$xc,$yc,$this->la[$i],
			       $this->radius + $this->explode_radius[$i]); 
	}
    }

    // Position the labels of each slice
    function StrokeLabel($label,$img,$xc,$yc,$a,$r) {

	// Default value
	if( $this->ilabelposadj === 'auto' )
	    $this->ilabelposadj = 0.65;

	// We position the values diferntely depending on if they are inside
	// or outside the pie
	if( $this->ilabelposadj < 1.0 ) {

	    $this->value->SetAlign('center','center');
	    $this->value->margin = 0;
	    
	    $xt=round($this->ilabelposadj*$r*cos($a)+$xc);
	    $yt=round($yc-$this->ilabelposadj*$r*sin($a));
	    
	    $this->value->Stroke($img,$label,$xt,$yt);
	}
	else {

	    $this->value->halign = "left";
	    $this->value->valign = "top";
	    $this->value->margin = 0;
	    
	    $r += $img->GetFontHeight()/2;
	    $xt=round($r*cos($a)+$xc);
	    $yt=round($yc-$r*sin($a));
	    
	    // Position the axis title. 
	    // dx, dy is the offset from the top left corner of the bounding box that sorrounds the text
	    // that intersects with the extension of the corresponding axis. The code looks a little
	    // bit messy but this is really the only way of having a reasonable position of the
	    // axis titles.
	    $img->SetFont($this->value->ff,$this->value->fs,$this->value->fsize);
	    $h=$img->GetTextHeight($label);
	    $w=$img->GetTextWidth(sprintf($this->value->format,$label));
	    while( $a > 2*M_PI ) $a -= 2*M_PI;
	    if( $a>=7*M_PI/4 || $a <= M_PI/4 ) $dx=0;
	    if( $a>=M_PI/4 && $a <= 3*M_PI/4 ) $dx=($a-M_PI/4)*2/M_PI; 
	    if( $a>=3*M_PI/4 && $a <= 5*M_PI/4 ) $dx=1;
	    if( $a>=5*M_PI/4 && $a <= 7*M_PI/4 ) $dx=(1-($a-M_PI*5/4)*2/M_PI);
	    
	    if( $a>=7*M_PI/4 ) $dy=(($a-M_PI)-3*M_PI/4)*2/M_PI;
	    if( $a<=M_PI/4 ) $dy=(1-$a*2/M_PI);
	    if( $a>=M_PI/4 && $a <= 3*M_PI/4 ) $dy=1;
	    if( $a>=3*M_PI/4 && $a <= 5*M_PI/4 ) $dy=(1-($a-3*M_PI/4)*2/M_PI);
	    if( $a>=5*M_PI/4 && $a <= 7*M_PI/4 ) $dy=0;

	    $this->value->Stroke($img,$label,$xt-$dx*$w,$yt-$dy*$h);
	}
    }	
} // Class


//===================================================
// CLASS PiePlotC
// Description: Same as a normal pie plot but with a 
// filled circle in the center
//===================================================
class PiePlotC extends PiePlot {
    var $imidsize=0.5;		// Fraction of total width
    var $imidcolor='white';
    var $midtitle='';
    var $middlecsimtarget="",$middlecsimalt="";

    function PiePlotC($data,$aCenterTitle='') {
	parent::PiePlot($data);
	$this->midtitle = new Text();
	$this->midtitle->ParagraphAlign('center');
    }

    function SetMid($aTitle,$aColor='white',$aSize=0.5) {
	$this->midtitle->Set($aTitle);
	$this->imidsize = $aSize ; 
	$this->imidcolor = $aColor ; 
    }

    function SetMidTitle($aTitle) {
	$this->midtitle->Set($aTitle);
    }

    function SetMidSize($aSize) {
	$this->imidsize = $aSize ; 
    }

    function SetMidColor($aColor) {
	$this->imidcolor = $aColor ; 
    }

    function SetMidCSIM($aTarget,$aAlt) {
	$this->middlecsimtarget = $aTarget;
	$this->middlecsimalt = $aAlt;
    }

    function AddSliceToCSIM($i,$xc,$yc,$radius,$sa,$ea) {  
        //Slice number, ellipse centre (x,y), radius, start angle, end angle
	while( $sa > 2*M_PI ) $sa = $sa - 2*M_PI;
	while( $ea > 2*M_PI ) $ea = $ea - 2*M_PI;

	$sa = 2*M_PI - $sa;
	$ea = 2*M_PI - $ea;

	// Add inner circle first point
	$xp = floor(($this->imidsize*$radius*cos($ea))+$xc);
	$yp = floor($yc-($this->imidsize*$radius*sin($ea)));
	$coords = "$xp, $yp";
	
	//add coordinates every 0.25 radians
	$a=$ea+0.25;
	while ($a < $sa) {
	    $xp = floor(($this->imidsize*$radius*cos($a)+$xc));
	    $yp = floor($yc-($this->imidsize*$radius*sin($a)));
	    $coords.= ", $xp, $yp";
	    $a += 0.25;
	}

	// Make sure we end at the last point
	$xp = floor(($this->imidsize*$radius*cos($sa)+$xc));
	$yp = floor($yc-($this->imidsize*$radius*sin($sa)));
	$coords.= ", $xp, $yp";

	// Straight line to outer circle
	$xp = floor($radius*cos($sa)+$xc);
	$yp = floor($yc-$radius*sin($sa));
	$coords.= ", $xp, $yp";	

	//add coordinates every 0.25 radians
	$a=$sa - 0.25;
	while ($a > $ea) {
	    $xp = floor($radius*cos($a)+$xc);
	    $yp = floor($yc-$radius*sin($a));
	    $coords.= ", $xp, $yp";
	    $a -= 0.25;
	}
		
	//Add the last point on the arc
	$xp = floor($radius*cos($ea)+$xc);
	$yp = floor($yc-$radius*sin($ea));
	$coords.= ", $xp, $yp";

	// Close the arc
	$xp = floor(($this->imidsize*$radius*cos($ea))+$xc);
	$yp = floor($yc-($this->imidsize*$radius*sin($ea)));
	$coords .= ", $xp, $yp";

	if( !empty($this->csimtargets[$i]) )
	    $this->csimareas .= "<area shape=\"poly\" coords=\"$coords\" href=\"".
		$this->csimtargets[$i]."\"";
	if( !empty($this->csimalts[$i]) ) {
	    $tmp=sprintf($this->csimalts[$i],$this->data[$i]);
	    $this->csimareas .= " alt=\"$tmp\" title=\"$tmp\"";
	}
	$this->csimareas .= ">\n";
    }


    function Stroke($img) {

	// Stroke the pie but don't stroke values
	$tmp =  $this->value->show;
	$this->value->show = false;
	parent::Stroke($img);
	$this->value->show = $tmp;

 	$xc = round($this->posx*$img->width);
	$yc = round($this->posy*$img->height);

	if( $this->ishadowcolor != "" ) {
	    $img->SetColor($this->ishadowcolor);
	    $img->FilledCircle($xc+$this->ishadowdrop,$yc+$this->ishadowdrop,
			       round($this->radius*$this->imidsize));
	}

	if( $this->imidsize > 0 ) {

	    $img->SetColor($this->imidcolor);
	    $img->FilledCircle($xc,$yc,round($this->radius*$this->imidsize));

	    if(  $this->pie_border ) {
		$img->SetColor($this->color);
		$img->Circle($xc,$yc,round($this->radius*$this->imidsize));
	    }

	    if( !empty($this->middlecsimtarget) )
		$this->AddMiddleCSIM($xc,$yc,round($this->radius*$this->imidsize));
	}



	if( $this->value->show ) 
	    $this->StrokeAllLabels($img,$xc,$yc);

	$this->midtitle->Pos($xc,$yc,'center','center');
	$this->midtitle->Stroke($img);

    }

    function AddMiddleCSIM($xc,$yc,$r) {
	$this->csimareas .= "<area shape=\"circle\" coords=\"$xc,$yc,$r\" href=\"".
	    $this->middlecsimtarget."\"";
	if( !empty($this->middlecsimalt) ) {
	    $tmp = $this->middlecsimalt;
	    $this->csimareas .= " alt=\"$tmp\" title=\"$tmp\"";
	}
	$this->csimareas .= ">\n";
    }

    function StrokeLabel($label,$img,$xc,$yc,$a,$r) {

	if( $this->ilabelposadj === 'auto' )
	    $this->ilabelposadj = (1-$this->imidsize)/2+$this->imidsize;

	parent::StrokeLabel($label,$img,$xc,$yc,$a,$r);

    }

}


//===================================================
// CLASS PieGraph
// Description: 
//===================================================
class PieGraph extends Graph {
    var $posx, $posy, $radius;		
    var $legends=array();	
    var $plots=array();
//---------------
// CONSTRUCTOR
    function PieGraph($width=300,$height=200,$cachedName="",$timeout=0,$inline=1) {
	$this->Graph($width,$height,$cachedName,$timeout,$inline);
	$this->posx=$width/2;
	$this->posy=$height/2;
	$this->SetColor(array(255,255,255));		
    }

//---------------
// PUBLIC METHODS	
    function Add(&$pie) {
	$this->plots[] = $pie;
    }
	
    function SetColor($c) {
	$this->SetMarginColor($c);
    }


    function DisplayCSIMAreas() {
	    $csim="";
	    foreach($this->plots as $p ) {
		$csim .= $p->GetCSIMareas();
	    }
	    //$csim.= $this->legend->GetCSIMareas();
	    if (preg_match_all("/area shape=\"(\w+)\" coords=\"([0-9\, ]+)\"/", $csim, $coords)) {
		$this->img->SetColor($this->csimcolor);
		for ($i=0; $i<count($coords[0]); $i++) {
		    if ($coords[1][$i]=="poly") {
			preg_match_all('/\s*([0-9]+)\s*,\s*([0-9]+)\s*,*/',$coords[2][$i],$pts);
			$this->img->SetStartPoint($pts[1][count($pts[0])-1],$pts[2][count($pts[0])-1]);
			for ($j=0; $j<count($pts[0]); $j++) {
			    $this->img->LineTo($pts[1][$j],$pts[2][$j]);
			}
		    } else if ($coords[1][$i]=="rect") {
			$pts = preg_split('/,/', $coords[2][$i]);
			$this->img->SetStartPoint($pts[0],$pts[1]);
			$this->img->LineTo($pts[2],$pts[1]);
			$this->img->LineTo($pts[2],$pts[3]);
			$this->img->LineTo($pts[0],$pts[3]);
			$this->img->LineTo($pts[0],$pts[1]);
						
		    }
		}
	    }
    }

    // Method description
    function Stroke($aStrokeFileName="") {
	// If the filename is the predefined value = '_csim_special_'
	// we assume that the call to stroke only needs to do enough
	// to correctly generate the CSIM maps.
	// We use this variable to skip things we don't strictly need
	// to do to generate the image map to improve performance
	// a best we can. Therefor you will see a lot of tests !$_csim in the
	// code below.
	$_csim = ($aStrokeFileName===_CSIM_SPECIALFILE);

	// We need to know if we have stroked the plot in the
	// GetCSIMareas. Otherwise the CSIM hasn't been generated
	// and in the case of GetCSIM called before stroke to generate
	// CSIM without storing an image to disk GetCSIM must call Stroke.
	$this->iHasStroked = true;

		
	if( !$_csim ) {
	    if( $this->background_image != "" ) {
		$this->StrokeFrameBackground();		
	    }
	    else {
		$this->StrokeFrame();		
	    }
	}
		
	for($i=0; $i<count($this->plots); ++$i) 
	    $this->plots[$i]->Stroke($this->img);
	if( !$_csim ) {	
	    foreach( $this->plots as $p)
		$p->Legend($this);	
	    
	    $this->legend->Stroke($this->img);
	    $this->StrokeTitles();

	    // Stroke texts
	    if( $this->texts != null )
		foreach( $this->texts as $t) 
		    $t->Stroke($this->img);

	    if( JPG_DEBUG ) {
		$this->DisplayCSIMAreas();
	    }
		
	    // Finally output the image
	    $this->cache->PutAndStream($this->img,$this->cache_name,$this->inline,$aStrokeFileName);					
	}
    }
} // Class

/* EOF */
?>
