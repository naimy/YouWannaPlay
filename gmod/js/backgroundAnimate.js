


$(document).ready(function(){ 
       

var cycle = 0;

function timeOutFunctions() {
	first = setTimeout(OneToTwo, 0);
    second = setTimeout(TwoToThree, 2000);
	third = setTimeout(ThreeToFour, 4000);
	fourth = setTimeout(FourToFive, 6000);
	fifth = setTimeout(FiveToSix, 8000);
	end = setTimeout(End, 10000);

}

	
	function OneToTwo() {
		if ($('#background1').css("opacity") > 0)
		{
			$('#background1').animate({opacity:'0'}, 2000);
			$('#background2').animate({opacity:'1'}, 2000);
		}
	}
	
	function TwoToThree() {
		if ($('#background2').css("opacity") > 0)
		{
			$('#background2').animate({opacity:'0'}, 2000);
			$('#background3').animate({opacity:'1'}, 2000);
		}	
	}
	
	function ThreeToFour() {
		if ($('#background3').css("opacity") > 0)
		{
			$('#background3').animate({opacity:'0'}, 2000);
			$('#background4').animate({opacity:'1'}, 2000);
		}	
	}
	
	function FourToFive() {
		if ($('#background4').css("opacity") > 0)
		{
			$('#background4').animate({opacity:'0'}, 2000);
			$('#background5').animate({opacity:'1'}, 2000);
		}	
	}
	
	function FiveToSix() {
		if ($('#background5').css("opacity") > 0)
		{
			$('#background5').animate({opacity:'0'}, 2000);
			$('#background6').animate({opacity:'1'}, 2000);
		}	
	}
		
	function End() {
		if ($('#background6').css("opacity") > 0)
		{
			$('#background6').animate({opacity:'0'}, 2000);
			$('#background1').animate({opacity:'1'}, 2000);
		}
		timer();
	}

	var timer = function() {
		timeOutFunctions();
	}
	timer();

	
});


