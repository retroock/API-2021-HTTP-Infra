var Chance = require('chance');
var chance = new Chance();

var express = require('express');
var app = express();

app.get('/', function(req, res){
	res.send(generateAnimals());
});

app.listen(3000, function(){
});

function generateAnimals(){
	var numberOfAnimals = chance.integer({
		min: 5,
		max: 15
	});
	var animals = [];
	for(var i = 0; i < numberOfAnimals; i++){
		var gender = chance.gender();
		var birthYear = chance.year({
			min: 1980,
			max: 2010
		});
		var name = chance.first({gender:gender});
		var animal = chance.animal({type: 'zoo'});
		animals.push({
			animal: animal,
			gender: gender,
			birthYear: birthYear,
			name: name			
		});
	}
	return animals;
}