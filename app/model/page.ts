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
			console.error(resp.error.code,resp.error.message);
		}
		return resp.data;
	}
}

class LogView extends Backbone.View <LogModel> {
	template: any;
	constructor(args:any) {
		super(args);
		this.el = $('#LogView tbody')[0];
		this.template = _.template($('#LogTemplate').html());
		this.collection = new LogCollection({});
		this.collection.fetch({
			data: "axesLog" // [TODO]: gets.phpとしてまとめて引数で管理したい旨
		});
		this.listenTo(this.collection, 'sync', this.render);
	}
	render(){
		this.docWrite({id:'default'});
		return this;
	}
	docWrite(attr:{id:string,val?:string|number}){
		this.el.innerHTML = '';
		switch (attr.id) {
			case "default":
				this.collection.each((model)=>{
					this.el.innerHTML += this.template(model.toJSON());
				});
				this.setEvents();
				break;
			default:
				attr.val = attr.val === undefined ? '' : attr.val;
				_.each(this.collectionSelectAtCategory(attr.id, attr.val), (model)=>{
					this.el.innerHTML += this.template(model.toJSON());
				});
				break;
		}
	}
	setEvents(){
		_.each(document.querySelectorAll("#LogView .logFilter"), (v)=>{
			v.addEventListener('click', (ev:any)=>{ console.log(ev.target.textContent);this.docWrite({id:ev.target.dataset.name, val:ev.target.textContent});}, false);
		});
		document.querySelector('#ResetFilter').addEventListener('click', ()=>{this.docWrite({id:'default'});}, false);
	}
	collectionSelectAtCategory(cat:string, val:string|number){
		return this.collection.filter((model:any)=>{ return val == _.property(cat)(model.attributes);});
	}
}

class LogFilterView extends Backbone.View<Backbone.Model> {
	template :any
	preTemplate :any
	postTemplate :string = '</select>'
	constructor(args :any) {
		super(args)
		this.preTemplate = _.template('<select class="form-control" data-name="<%- name %>">')
		this.template = _.template('<option><%- value %></option>')
		this.el = $('#LogView thead th')
	}
}



new LogView({});
