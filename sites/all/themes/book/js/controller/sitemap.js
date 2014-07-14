var arrowUpClass = 'fa fa-angle-up';
var arrowDownClass = 'fa fa-angle-down';

// Continent toggle

var continents = document.querySelectorAll('.country-name');

for (var i = 0; i < continents.length; i++)
	continents[i].onclick = function() {
		var countries = this.parentNode.querySelector('.continent-countries');
		var arrow = this.querySelector('i');
		
		if (countries) {
			if (countries.style.display == 'block'){
				countries.style.display = 'none';
			}
			else {
				countries.style.display = 'block';
			}
		}
		
		if (arrow) {
			if (arrow.className == arrowDownClass)
				arrow.className = arrowUpClass;
			else
				arrow.className = arrowDownClass;
		}
	}
	
// Expand continents

document.getElementById('expand').onchange = function() {
	expandContinents(this.checked);
}

function expandContinents(expand) {
	var countries = document.querySelectorAll('.continent-countries');
	
	for (var i = 0; i < countries.length; i++)
		countries[i].style.display = expand ? 'block' : 'none';
		
	var arrows = document.querySelectorAll('h1 i');
	
	for (var i = 0; i < arrows.length; i++)
		arrows[i].className = expand ? arrowUpClass : arrowDownClass;
}