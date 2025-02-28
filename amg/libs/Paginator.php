<?php

class Paginator{
    var $items_per_page;
    var $items_total;
    var $active_page;
    var $num_pages;
    var $mid_range;
    var $low;
    var $high;
    var $limit;
    var $return;
    var $default_ipp = 50;



    function __construct()
    {
		$this->active_page = 1;
        $this->mid_range = 7;
        $this->items_per_page = (!empty($_GET['ipp'])) ? $_GET['ipp'] : $this->default_ipp;
    }

    function paginate($link)
    {

		if(@$_GET['ipp'] == 'All')
        {
            $this->num_pages = ceil($this->items_total/$this->default_ipp);
            $this->items_per_page = $this->default_ipp;
        }
        else
        {
            if(!is_numeric($this->items_per_page) OR $this->items_per_page <= 0) $this->items_per_page = $this->default_ipp;
            $this->num_pages = ceil($this->items_total/$this->items_per_page);
        }
        @$this->active_page = (int) $_GET['page-no']; // must be numeric > 0
        if($this->active_page < 1 Or !is_numeric($this->active_page)) $this->active_page = 1;
        if($this->active_page > $this->num_pages) $this->active_page = $this->num_pages;
        $prev_page = $this->active_page-1;
        $next_page = $this->active_page+1;

        if($this->num_pages > 10)
        {
            $this->return = ($this->active_page != 1 And $this->items_total >= 10) ? "<li><a href=\"".$link."page-no=$prev_page&ipp=$this->items_per_page\" data-original-title='' title=''>«</a></li>":"<li>«</li>";

            $this->start_range = $this->active_page - floor($this->mid_range/2);
            $this->end_range = $this->active_page + floor($this->mid_range/2);

            if($this->start_range <= 0)
            {
                $this->end_range += abs($this->start_range)+1;
                $this->start_range = 1;
            }
            if($this->end_range > $this->num_pages)
            {
                $this->start_range -= $this->end_range-$this->num_pages;
                $this->end_range = $this->num_pages;
            }
            $this->range = range($this->start_range,$this->end_range);

            for($i=1;$i<=$this->num_pages;$i++)
            {
                if($this->range[0] > 2 And $i == $this->range[0]) $this->return .= "";
                // loop through all pages. if first, last, or in range, display
                if($i==1 Or $i==$this->num_pages Or in_array($i,$this->range))
                {
                    $this->return .= ($i == $this->active_page And isset($_GET['page-no']) != 'All') ? "<li><a title=\"Go to page $i of $this->num_pages\" class=\"active\" href=\"javascript:void(0);\"><b>$i</b></a></li> ":"<li><a  title=\"Go to page $i of $this->num_pages\" href=\"".$link."page-no=$i&ipp=$this->items_per_page\" data-original-title='' title=''>$i</a></li> ";
                }
                if($this->range[$this->mid_range-1] < $this->num_pages-1 AND $i == $this->range[$this->mid_range-1]) $this->return .= "";
            }
            $this->return .= (($this->active_page != $this->num_pages And $this->items_total >= 10) And (isset($_GET['page-no']) != 'All')) ? "<li><a href=\"".$link."page-no=$next_page&ipp=$this->items_per_page\" data-original-title='' title=''>»</li>":"<li>»</li>";
            $this->return .= (isset($_GET['page-no']) == 'All') ? "<li><a href=\"javascript:void(0);\" data-original-title='' title=''>All</a></li> \n":"<li><a href=\"".$link."page-no=1&ipp=All\">All</a></li> \n";
        }
        else
        {
            for($i=1;$i<=$this->num_pages;$i++)
            {
                $this->return .= ($i == $this->active_page) ? "<li><a href=\"javascript:void(0);\" class='active'>$i</a></li> ":"<li><a href=\"".$link."page-no=$i&ipp=$this->items_per_page\">$i</a></li> ";
            }
            $this->return .= "<li><a href=\"".$link."page-no=1&ipp=All\">All</a> \n</li>";
        }
        $this->low = ($this->active_page-1) * $this->items_per_page;
        @$this->high = ($_GET['ipp'] == 'All') ? $this->items_total:($this->active_page * $this->items_per_page)-1;
        @$this->limit = ($_GET['ipp'] == 'All') ? "":" LIMIT $this->low,$this->items_per_page";
    }

    function display_items_per_page($link)
    {


		$items = '';
        $ipp_array = array(10,25,50,100,250,500,'All');
        foreach($ipp_array as $ipp_opt)    $items .= ($ipp_opt == $this->items_per_page) ? "<option selected value=\"$ipp_opt\">$ipp_opt</option>\n":"<option value=\"$ipp_opt\">$ipp_opt</option>\n";
        //here goes heading after span
		return "<li><select class=\"paginate\" style=\"width:60px;\" onchange=\"window.location='".$link."page-no=1&ipp='+this[this.selectedIndex].value;return false\">$items</select></li>\n";
    }

    function display_jump_menu($link)
    {


		for($i=1;$i<=$this->num_pages;$i++)
        {
            @$option .= ($i==$this->active_page) ? "<option value=\"$i\" selected>$i</option>\n":"<option value=\"$i\">$i</option>\n";
        }
		//here goes heading after span
        return "<li><select class=\"paginate\" style=\"width:60px;\" onchange=\"window.location='".$link."page-no='+this[this.selectedIndex].value+'&ipp=$this->items_per_page';return false\">$option</select></li>\n";
    }

    function display_pages()
    {
		return $this->return;
    }




}
?>
