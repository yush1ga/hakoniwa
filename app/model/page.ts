// import $ = require("jquery");
// // import _ = require("underscore");
// import Backbone = require("backbone");


class dataModel extends Backbone.Model {
	constructor(args?:any) {
		super(args);
		this.getLogData()
			.done((data)=>{
				this.set(this.parse(data,','));
			}).fail((err)=>{
				this.set(err);
				console.error('err');
		});
	}
	parse(data:any, options:string){
		options = options;
		return data;
		// let parsedData: any[] = [];
		// for (let i=0, len=data.length; i < len; i++){

		// 	parsedData[i] = data[i].split(options);
		// }
		// return parsedData;
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
}

// class LogCollection extends Backbone.Collection <Backbone.Model> {
// 	constructor(args?:any) {
// 		super(args);
// 		this.model = 'dataModel';
// 	}
// }

class dataView extends Backbone.View <Backbone.Model> {
	template: any;
	constructor(args?:any) {
		super(args);
		this.el = document.getElementById('Out');
		this.template = _.template(this.el.innerHTML);
		this.model = new dataModel({});
		this.listenTo(this.model, 'change', this.render);
	}
	render(){
		_.each(this.model.attributes, (v)=>{
			console.dir(v);
			let a = [v]
			this.el.innerHTML += this.template(a);
			// _.map(v,(w:any, i:string)=>{
			// 	this.el.innerHTML += "("+i+")"+w + "　";
			// });
			// this.el.innerHTML += "<br>\n";
		})
		this.el.style.display = '';
		console.dir(this.model.attributes);
		return this;
	}
	sort(ev:any){
		console.dir(ev);
	}
}

new dataView({});
