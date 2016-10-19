import Backbone = require("backbone");
import _ = require("underscore");


class dataModel extends Backbone.Model {
	public attributes:any
	constructor(args?:any) {
		super(args);
	}
}

class dataView extends Backbone.View <Backbone.Model> {
	template:any;
	constructor(args?:any) {
		super(args);
		this.el = document.getElementById('Out');
		this.template = _.template(document.getElementById('Tpl').innerHTML) || 'aa';
		this.model = new dataModel({});


	}
	render(){
		this.el.innerHTML = this.template(this.model.attributes);
		return this;
	}
	sort(ev:any){
		console.dir(ev);
	}
}

()=>{ new dataView({});}
