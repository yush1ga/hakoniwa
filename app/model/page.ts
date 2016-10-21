// import $ = require("jquery");
// import _ = require("underscore");
// import Backbone = require("backbone");


class LogModel extends Backbone.Model {
	constructor(args:any) {
		super(args);
		this.getLogData()
			.done((data)=>{
				this.set(data);
			}).fail((err)=>{
				console.error('err');
				console.dir(err);
		});
	}
	private getLogData(){
		let defer = $.Deferred();
		$.ajax({
			url : "axes-getLogs.php",
			data: "axesLog", // [TODO]: gets.phpとしてまとめて引数で管理したい旨
			dataType: 'json',
			method  : 'POST',
			success : defer.resolve,
			error   : defer.reject
		});
		return defer.promise();
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
	}
}

class dataView extends Backbone.View <Backbone.Model> {
	template: any;
	constructor(args:any) {
		super(args);
		this.el = document.getElementById('Out');
		this.template = _.template(this.el.innerHTML);
		this.collection = new LogCollection({});
		// this.model = new LogModel({});
		this.listenTo(this.collection, 'fetch', this.render);
	}
	render(){
		_.each(this.collection.models, (v)=>{
			console.dir(v);
			this.el.innerHTML += this.template(v);
		})
		this.el.style.display = '';
		return this;
	}
	sort(ev:any){
		console.dir(ev);
	}
}

new dataView({});
