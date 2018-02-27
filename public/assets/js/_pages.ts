'use strict'

class AutoFilter {
	private _labelFilterBlank: string;
	private _labelFilterAll  : string;
	private _valFilterAll    : string;
	private _elmTBody        : any;

	constructor() {
		// config
		this._labelFilterBlank = "(Empty)";
		this._labelFilterAll   = "All";
		this._valFilterAll     = "__all__";
		// /config
		this._elmTBody = null;
 	}

 	/**
 	 * フィルタ生成
	 * @param  tblId :string オートフィルタを表示する table の ID
	 * @return       :number 状態 (1:フィルタ生成済、0:実行完了)
	**/
	public createFilter(tblId: string): number {
		// Guard
		if (this._elmTBody != null) return 1

		let elmTable = <HTMLElement>document.getElementById(tblId);
		let elmTbody = <HTMLElement>elmTable.getElementsByTagName("tbody").item(0);
		let elmTr = elmTbody.getElementsByTagName("tr");
		let elmFilter = this.createElement({ el:"tr" });

		// table の内容取得とフィルタ表示用の要素生成
		let arrayCols = new Array();
		for (let row_i = 0, len_i = elmTr.length; row_i < len_i; row_i++) {
			let alias_childNodes = elmTr[row_i].childNodes;

			for (let col_j = 0, len_j = alias_childNodes.length; col_j < len_j; col_j++) {
				let alias_col = alias_childNodes[col_j];

				if (alias_col.nodeType !== 1) {
					// 要素以外を取り除く
					elmTr[row_i].removeChild(alias_col);
					col_j--;
					continue;
				}

				if (row_i === 0) {
					const elmTh_head = this.createElement({
						el: "th",
						attr: { scope: "row"}
					});
					elmFilter.appendChild(elmTh_head);
					arrayCols[col_j] = new Array();
				}

				// tips: 縦列を使ってフィルタするので、(列.行)のほうが都合が良いっぽい
				arrayCols[col_j][row_i] = alias_col.textContent;
			}
		}
		this._elmTBody = elmTbody.cloneNode(true);

		// フィルタを 1 つずつ追加していくとカクカクとした表示となるため
		// "display: none"にして追加を行う
		let elmThead = elmTable.getElementsByTagName("thead").item(0);
		elmFilter.style.display = "none";
		elmThead.appendChild(elmFilter);
		this.rewriteFilter(elmFilter, arrayCols);
		try {
			elmFilter.style.display = "table-row";
		} catch (e) {
			e='';
			elmFilter.style.display = "block";
		}

		return 0;
	}


	/**
	 * 要素生成
	 * @param  argv :Object  要素の情報
	 * @return      :HTMLElement  生成された要素
	 */
	private createElement(argv :{el:string,attr?:any,content?:string}): HTMLElement {
		let elm = document.createElement(argv.el);
		if (argv.attr) {
			const attrs = argv.attr;
			const attr_keys = Object.keys(attrs);
			for (let i in attr_keys) {
				elm.setAttribute(attr_keys[i], attrs[i]);
			}
		}
		if (argv.content) {
			elm.appendChild(document.createTextNode(argv.content));
		}
		return elm;
	}

	/**
	 * select要素の選択されているvalue属性値を取得
	 * @param  elmSelect  select要素
	 * @return            選択されている value 属性値
	 */
	private getSelectValue(elmSelect:any): string|number {
		return elmSelect.options[elmSelect.selectedIndex].value;
	}

	selectFilter(elmSelect :HTMLElement){
		let arrayFilters = new Array()
		let areAllFilters_valFilterAll :boolean = true
		let elmTrFilter = elmSelect.parentNode.parentNode
		// let elmSelect = elmTrFilter.getElementsByTagName('select')

		for (let colN = 0, len = elmSelect.length ; colN < len; ++colN) {
			arrayFilters[colN] = this.getSelectValue(elmSelect[colN])
			if(arrayFilters[colN]!==this._valFilterAll) areAllFilters_valFilterAll = false
		}

		let elmTbody_new = this._elmTBody.cloneNode(true)
		let elmTRBody = <HTMLElement>elmTbody_new.getElementsByTagName('tr')

		if(!areAllFilters_valFilterAll){
			// いずれかのフィルタで VAL_FILTER_ALL 以外を選択

			for (let row_i = 0, len_i = elmTRBody.length; row_i < len_i; row_i++) {
				let alias_row = elmTRBody[row_i]
				for (let colN = 0, childNodesLen = alias_row.childNodes.length; colN < childNodesLen; colN++) {
					if (arrayFilters[colN] === areAllFilters_valFilterAll) continue

					let strCell = this.getTextContent(alias_row.childNodes[colN])
					if (strCell === null) strCell = ""

					if (arrayFilters[colN] !== areAllFilters_valFilterAll && arrayFilters[colN] !== strCell) {
						// フィルタ適用
						elmTbody_new.removeChild(alias_row);
						row_i--;
						break;
					}
				}
			}
		}

		let arrayCols = []; // フィルタ用の列データ
		for (let row_i = 0, len = elmTRBody.length; row_i < len; row_i++) {
			let alias_row = elmTRBody[row_i]
			for (let col_j = 0, childNodesLen = alias_row.childNodes.length; col_j < childNodesLen; col_j++) {
				if (arrayCols[col_j] === null) arrayCols[col_j] = []
				arrayCols[col_j][row_i] = this.getTextContent(alias_row.childNodes[col_j]);
			}
		}
		this.rewriteFilter(elmTrFilter, arrayCols);

		let elmThead = elmTrFilter.parentNode;
		var elmTable = elmThead.parentNode;
		var elmTbody = <Node>elmTable.getElementsByTagName("tbody").item(0);
		elmTable.removeChild(elmTbody);
		elmTable.appendChild(elmTbody_new);
	}
	/**
	 * ufo演算子相当
	 * @param {any}:maybe Number a
	 * @param {any}:maybe Number b
	 * @return number a:-1,0,1:b
	 */
	compareFilter(a :any, b :any) :number{
		try{
			if(!isNaN(a) && !isNaN(b)) {
				a = Number(a)
				b = Number(b)
			}
		}catch(e:any){
			console.error(e)
		}
		return (a < b)? 1: (a > b)? -1: 0
	}

	private rewriteFilter(elmTrFilter, arrayCols){
		let elmSelect = <NodeList>elmTrFilter.getElementsByTagName('select')
		if(elmSelect.length === 0) elmSelect = null

		for (let col_i = 0, arrayColsLen :number = arrayCols.length; col_i < arrayColsLen; col_i++) {
			let alias_cols = arrayCols[col_i]

			alias_cols.sort(this.compareFilter)

			let elmSelect_new = (elmSelect != null)? elmSelect[col_i].cloneNode(false): this.createElement({ el: "select"})
			elmSelect_new.on('change', (v)=>{this.selectFilter(v)})
			let strSelect = null
			let elmOption :HTMLOptionElement = <HTMLOptionElement>this.createElement({
				el: "option",
				attr: { value: this._valFilterAll},
				content: this._labelFilterAll
			})

			if (elmSelect == null) {
				elmOption.defaultSelected = true
				elmOption.selected = true
			} else {
				strSelect = this.getSelectValue(elmSelect[col_i]);
				if (strSelect == this._valFilterAll) {
					elmOption.defaultSelected = true
					elmOption.selected = true
				}
			}

			elmSelect_new.appendChild(elmOption)

			for (let i = 0, colsLen = alias_cols.length; i < colsLen; i++) {
				if (i > 0 && alias_cols[i] != alias_cols[i - 1] || i == 0) {
					let alias_col = alias_cols[i]
					let strValue :string = ''
					let strContent = null

					if (alias_col != null && alias_col.length > 0) {
						// セルが空欄以外
						strValue = alias_col
						strContent = strValue
					} else {
						// セルが空欄
						strValue = ""
						strContent = this._labelFilterBlank
						alias_col = strValue;
					}

					elmOption = <HTMLOptionElement>this.createElement({
						el: "option",
						attr: { value: strValue},
						content: strContent
					});

					if (strSelect == alias_col) {
						elmOption.defaultSelected = true
						elmOption.selected = true
					}
					elmSelect_new.appendChild(elmOption);
				}
			}

			if (elmSelect != null) {
			  elmTrFilter.childNodes[col_i].removeChild(elmSelect[col_i])
			}
			elmTrFilter.childNodes[col_i].appendChild(elmSelect_new);
		}
	}


}

// ===== ボタンの処理 =========================================


//
// オートフィルタを表示するボタン
//
//    @param elmInput ボタンの要素
//    @param strId    オートフィルタを表示する table の ID
//
function Button_DispFilter(elmInput, strId) {
	// "display : none"を用いてオートフィルタの表示/非表示を切り替えると
	// Opera8.54 では一度非表示にしたあと select 要素で選択されている物を
	// 変更しようとしても変更できなくなる
	// そのため“表示ボタン”の使用は一度限りとし、ボタンは自滅させる

	const strMes = "【注意】お使いの環境ではオートフィルタを表示できません"
	let boolExec = false

	const objInterval = setInterval(()=>{
		clearInterval(objInterval);
		(()=>{
			if (boolExec) return;
			!boolExec

			let autofilter = new AutoFilter();
			if (autofilter.createFilter(strId) < 0) {
				alert(strMes);
				return;
			}

			// ボタンの自滅
			let elmParent = elmInput.parentNode;
			elmParent.removeChild(elmInput);
		})
	}, 200);
}


class SortTable{
	private msg_alert :string = "【注意】お使いの環境ではソート機能を利用できません"
	private order_default :number = -1

	private orderDirection :number = this.order_default
	private arrayColumn = null
	private lastSortedColumn = []

	/**
	 * ソートボタン
	 * @param {string} stdIdTable  [対象<table>のid]
	 * @param {any[]}  arrayColumn [ソート条件を優先順に格納した配列]
	 */
	buttonSort(stdIdTable :string, arrayColumn :any[]){
		let boolExec = false
		let objInterval = setInterval(()=>{
			clearInterval(objInterval);
			(()=>{
				if(boolExec) return;
				!boolExec
				this.sortTable(stdIdTable, arrayColumn)
			})
		}, 200);
	}

	compare(trA, trB){
		let arrayColumn = this.arrayColumn
		let orderDirection = this.orderDirection
		let compareResult = 0

		let valA :any = null
		let valB :any = null

		for (let i = 0, arrayColumnLen = arrayColumn.length; i < arrayColumnLen && compareResult===0; i++) {
			let alias_arrayColumn = arrayColumn[i]

			valA = trA.childNodes[alias_arrayColumn].innerText;
			valB = trB.childNodes[alias_arrayColumn].innerText;

			if ((!isNaN(valA) || valA.length < 1) && (!isNaN(valB) || valB.length < 1)) {
				// セルが両方とも“数字か空白”なら数値としてソート
				// Number.NEGATIVE_INFINITY を空白の代替とする

				valA = (valA.length > 0) ? Number(valA): Number.NEGATIVE_INFINITY;
				valB = (valB.length > 0) ? Number(valB): Number.NEGATIVE_INFINITY;
			}

			if (valA < valB) {
				compareResult = (orderDirection === -1)? 1 : -1;
			} else if (valA > valB) {
				compareResult = (orderDirection !== -1)? 1 : -1;
			}
		}

		return compareResult;
	}

	private sortTable(strIdTable :string, arrayColumn :any[]){
		if (!document.getElementById || !document.removeChild) {
			alert(this.msg_alert); return;
		}

		let elmTable = <HTMLElement>document.getElementById(strIdTable);
		let elmTbody = elmTable.getElementsByTagName("tbody").item(0);
		let elmTr = elmTbody.getElementsByTagName("tr");
		let arrayTr = [];

		// 現在の内容を取得
		for (let i = 0, trLen = elmTr.length; i < trLen; i++) {
			let alias_tr = elmTr[i];
			let alias_childNodes = alias_tr.childNodes;
			for (let j = 0, childNodesLen = alias_childNodes.length; j < childNodesLen; j++) {
				if (alias_childNodes[j].nodeType !== Node.ELEMENT_NODE) {
					// 要素以外を取り除く
					alias_tr.removeChild(alias_childNodes[j]);
					j--;
				}
			}
			arrayTr[i] = alias_tr.cloneNode(true);
		}

		let alias_lastSortedColumn = this.lastSortedColumn;
		if (alias_lastSortedColumn[strIdTable] == null) alias_lastSortedColumn[strIdTable] = -1;

		// 同じ列のソートは、ソート方向を反転
		this.orderDirection = (alias_lastSortedColumn[strIdTable] == arrayColumn[0])? this.orderDirection * -1 : this.order_default;

		this.arrayColumn = arrayColumn;
		alias_lastSortedColumn[strIdTable] = arrayColumn[0];

		arrayTr.sort((elA, elB)=>{
			return this.compare(elA, elB);
		});

		// ソート結果
		let elmTbodyResult = elmTbody.cloneNode(false);
		for (let i = 0, trLen = arrayTr.length; i < trLen; i++) {
			elmTbodyResult.appendChild(arrayTr[i]);
		}
		elmTable.removeChild(elmTbody)
		elmTable.appendChild(elmTbodyResult)
	}
}
const sortTable = new SortTable();

/**
 * ======================================================================
 * 開発画面用
 * ======================================================================
 */

// 開発、観光画面
function Navi(position :number, img :string, title :string, pos :any, text :string, exp :any) {

	var StyElm = <HTMLElement>document.getElementById("NaviView");
	StyElm.style.display = '';

	var posx = pos.indexOf(",");
	// var posy = pos.indexOf(")");
	var x = pos.substring(1, posx);
	var winEvent = windowEvent();

	if (position === 1) {
		// right
		StyElm.style.marginLeft = ((x - 19) * 32 + 478) + 'px'
		StyElm.style.top = document.body.scrollTop + winEvent.clientY + 150 + 'px'
	} else {
		// left
		StyElm.style.marginLeft = ((x - 19) * 32 + 668) + 'px'
		StyElm.style.top = document.body.scrollTop + winEvent.clientY + 150 + 'px'
	}

	StyElm.innerHTML = "<table><tr><td class='M'><img class='NaviImg' src=" + img + "></td><td class='M'><div class='NaviTitle'>" + title + " " + pos + "<\/div><div class='NaviText'>" + <string>text.replace("\n", "<br>") + "</div></td></tr></table>";
	if (exp) {
		StyElm.innerHTML += "<div class='NaviText'>" + eval(exp) + "<\/div>";
	}
}

function NaviClose() {
	let StyElm = <HTMLElement>document.getElementById("NaviView")
	StyElm.style.display = "none"
}

function windowEvent() {
	if (window.event) return window.event;
	for (let a = arguments.callee.caller; a;) {
		let b = a.arguments[0];
		if (b && b.constructor == MouseEvent) return b;
		a = a.caller
	}
	return null
}
