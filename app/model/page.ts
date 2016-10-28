// import $ = require("jquery");
// import _ = require("underscore");
// import Backbone = require("backbone");


class LogModel extends Backbone.Model {
	constructor(args:any) {
		super(args);
		this.listenTo(this, 'invalid', (model:any, error:any) => {
			console.log(model, error);
		});
	}
	validate(attrs: any){
		if(!_.isNumber(attrs.islId)) {
			return "wrong island ID data";
		}
		return;
	}
}

class LogCollection extends Backbone.Collection <LogModel> {
	constructor(args:any) {
		super(args);
		this.model = LogModel;
		this.url = 'axes-getLogs.php';
	}
	parse(resp:any){
		if(resp.error){
			console.error(resp.error.message);
		}
		return resp.data;
	}
}

class dataView extends Backbone.View <Backbone.Model> {
	template: any;
	constructor(args:any) {
		super(args);
		this.el = document.getElementById('Out');
		this.template = _.template($('#LogTemplate').html());
		this.collection = new LogCollection({});
		this.collection.fetch({
			data: "axesLog" // [TODO]: gets.phpとしてまとめて引数で管理したい旨
		});
		this.listenTo(this.collection, 'sync', this.render);
		// this.events = {
		// 	"click .a": "sort"
		// };
	}
	render(){
		this.collection.each((model)=>{
			this.el.innerHTML += this.template(model.toJSON());
		});
		return this;
	}
	sort(ev:any){
		console.dir(ev);
	}
}

new dataView({});
