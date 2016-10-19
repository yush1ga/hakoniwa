import Backbone = require("backbone");
import _ = require("underscore");


class Model extends Backbone.Model {
	constructor(args: any) {
		super(args);
	}
}

class View extends Backbone.View<Model> {
	private template: any = _.template(document.getElementById('Tpl').innerHTML()) || 'aa'
	private elm: any = 'Out'
	private model = Model
	private events = {
		"click" : "sort"
	}
	constructor(args: any) {
		super(args);
		console.dir(this.events);
	}
	render(){
		this.elm.innerHTML = _.compile(this.template(this.model));
	}
	sort(ev:any){
		console.dir(ev);
	}
}

()=>{ new View({});}
