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

  /* フィルタ生成
    @param  tblId :string オートフィルタを表示する table の ID
    @return       :number 状態 (1:フィルタ生成済、0:実行完了)
  */
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
        } // if //

        if (row_i === 0) {
          const elmTh_head = this.createElement({
            element: "th",
            attr: {
              scope: "row"
            }
          });
          elmFilter.appendChild(elmTh_head);

          arrayCols[col_j] = new Array();
        } // if

        // tips: 縦列を使ってフィルタするので、(列.行)のほうが都合が良いっぽい
        arrayCols[col_j][row_i] = alias_col.textContent;
      } // for
    } // for
    this._elmTbody = elmTbody.cloneNode(true);

    // フィルタを 1 つずつ追加していくとカクカクとした表示となるため
    // “display : none”にして追加を行う
    var elmThead = elmTable.getElementsByTagName("thead").item(0);
    elmFilter.style.display = "none";
    elmThead.appendChild(elmFilter);
    this._Rewrite_Filter(elmFilter, arrayCols);
    try {
      elmFilter.style.display = "table-row";
    } catch () {
      elmFilter.style.display = "block";
    }

    return 0;
  }


  /* 要素生成
    @param  argv :Object  要素の情報
    @return      :HTMLElement  生成された要素
  */
  private createElement(argv :{el:string,attr?:string,content?:string}): HTMLElement {
    let elm = document.createElement(argv.el);
    if (argv.attr) {
      const attrs = argv.attr;
      const attr_keys  = Object.keys(attrs);
      for (let i in attr_keys) {
        elm.setAttribute(attr_keys[i], attrs[i]);
      } // for
    } // if
    if (argv.content) {
      elm.appendChild(document.createTextNode(argv.content));
    } // if
    return elm;
  }

  /* select要素の選択されているvalue属性値を取得
    @param  elmSelect  select要素
    @return            選択されている value 属性値
   */
  private getSelectValue(elmSelect:any): string|number {
    return elmSelect.options[elmSelect.selectedIndex].value;
  }


} // class AutoFilter


class ClassAutoFilter {
  private _labelFilterBlank :string = '(blank)'
  private _labelFilterAll :string = 'ALL'
  private _valFilterAll :string = '__all__'
  private elmTbody :any = null

  SelectFilter(elmSelect :HTMLElement){
    let arrayFilters = new Array()
    let areAllFilters_valFilterAll :boolean = true
    let elmTrFilter = elmSelect.parentNode.parentNode
    // let elmSelect = elmTrFilter.getElementsByTagName('select')

    for (let colN = 0, len = elmSelect.length ; colN < len; ++colN) {
      arrayFilters[colN] = this._getSelectValue(elmSelect[colN])
      if(arrayFilters[colN]!==this._valFilterAll) areAllFilters_valFilterAll = false
    }

    let elmTbody_new = this.elmTbody.cloneNode(true)
    let elmTRBody = <HTMLElement>elmTbody_new.getElementsByTagName('tr')

    if(!areAllFilters_valFilterAll){
    // いずれかのフィルタで VAL_FILTER_ALL 以外を選択

      for (let row_i = 0, len_i = elmTRBody.length; row_i < len_i; row_i++) {
        let alias_row = elmTRBody[row_i]
        for (let colN = 0, childNodesLen = alias_row.childNodes.length; colN < childNodesLen; colN++) {
          if (arrayFilters[colN] === areAllFilters_valFilterAll) continue

          let strCell = this._getTextContent(alias_row.childNodes[colN])
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

    let arrayCols = new Array(); // フィルタ用の列データ
    for (let row_i = 0, len = elmTRBody.length; row_i < len; row_i++) {
      let alias_row = elmTRBody[row_i]
      for (let col_j = 0, childNodesLen = alias_row.childNodes.length; col_j < childNodesLen; col_j++) {
        if (arrayCols[col_j] === null) arrayCols[col_j] = new Array()
        arrayCols[col_j][row_i] = this._getTextContent(alias_row.childNodes[col_j]);
      }
    }
    this._rewriteFilter(elmTrFilter, arrayCols);

    let elmThead = elmTrFilter.parentNode;
    var elmTable = elmThead.parentNode;
    var elmTbody = elmTable.getElementsByTagName("tbody").item(0);
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

  private _rewriteFilter(elmTrFilter, arrayCols){
    let elmSelect = <HTMLElement>elmTrFilter.getElementsByTagName('select')

  }


}



// ----- private な関数として扱う -----


//
// フィルタの select 要素の生成/書き換え
//
//    @param arrayCols 列データ
//
classAutofilter.prototype._Rewrite_Filter = function(elmTr_filter, arrayCols) {
  var elm_select = elmTr_filter.getElementsByTagName("select");

  if (elm_select.length == 0) elm_select = null;

  var numLen_arrayCols = arrayCols.length;
  for (var numCol = 0; numCol < numLen_arrayCols; numCol++) {
    var alias_cols = arrayCols[numCol];

    alias_cols.sort(this.Compare_Filter);

    var elmSelect_new = (elm_select != null) ? elm_select[numCol].cloneNode(false) : this._Create_Element({
      element: "select"
    });
    var class_pointer = this;
    elmSelect_new.onchange = function() {
      class_pointer.Select_Filter(this);
    };

    var strSelect = null;
    var elmOption = this._Create_Element({
      element: "option",
      attr: {
        value: this.VAL_FILTER_ALL
      },
      content: this.LABEL_FILTER_ALL
    });
    if (elm_select == null) {
      elmOption.defaultSelected = true;
      elmOption.selected = true;
    } else {
      strSelect = this._Get_SelectValue(elm_select[numCol]);
      if (strSelect == this.VAL_FILTER_ALL) {
        elmOption.defaultSelected = true;
        elmOption.selected = true;
      } // if //
    } // if //
    elmSelect_new.appendChild(elmOption);

    var numLen_cols = alias_cols.length;
    for (var i = 0; i < numLen_cols; i++) {
      if (i > 0 && alias_cols[i] != alias_cols[i - 1] || i == 0) {
        var alias_col = alias_cols[i];

        var strValue = null;
        var strContent = null;
        if (alias_col != null && alias_col.length > 0) {
          // セルが空欄以外

          strValue = alias_col;
          strContent = strValue;
        } else {
          // セルが空欄

          strValue = "";
          strContent = this.LABEL_FILTER_BLANK;
          alias_col = strValue;
        } // if //
        elmOption = this._Create_Element({
          element: "option",
          attr: {
            value: strValue
          },
          content: strContent
        });
        if (strSelect == alias_col) {
          elmOption.defaultSelected = true;
          elmOption.selected = true;
        } // if //
        elmSelect_new.appendChild(elmOption);
      } // if //
    } // for //

    if (elm_select != null) {
      elmTr_filter.childNodes[numCol].removeChild(elm_select[numCol]);
    } // if //
    elmTr_filter.childNodes[numCol].appendChild(elmSelect_new);
  } // for //
}; // classAutofilter.prototype._Rewrite_Filter //




// ===== ボタンの処理 =========================================


//
// オートフィルタを表示するボタン
//
//    @param elmInput ボタンの要素
//    @param strId    オートフィルタを表示する table の ID
//
function Button_DispFilter(elmInput, strId) {
  // “display : none”を用いてオートフィルタの表示/非表示を切り替えると
  // Opera 8.54 では一度非表示にしたあと select 要素で選択されている物を
  // 変更しようとしても変更できなくなる
  // そのため“表示ボタン”の使用は一度限りとし、ボタンは自滅させる

  var strMes = "お使いのブラウザではオートフィルタを表示できません";

  this.boolExec = false;
  var objInterval = setInterval(
    function() {
      clearInterval(objInterval);
      (function() {
        if (this.boolExec) return;

        this.boolExec = true;

        var cAutofilter = new classAutofilter();
        if (cAutofilter.Create_Filter(strId) < 0) {
          alert(strMes);
          return;
        } // if //

        // ボタンの自滅
        var elm_parent = elmInput.parentNode;
        elm_parent.removeChild(elmInput);
      })();
    }, // function //
    200
  );
} // Button_DispFilter //

/*
    table のソート 2
    http://neko.dosanko.us/script/sort_table2/
    2006-12-4 版

    とほほのWWW入門の「テーブルをソートする(2003/2/2版)」がベース
    http://www.tohoho-web.com/wwwxx038.htm
*/

function classSortTable() {
  // 設定 ここから↓

  this.MES_ALERT = "お使いのブラウザではソート機能を利用できません";

  this.ORDER_DEFAULT = -1;

  // 設定 ここまで↑

  this.numOrder = this.ORDER_DEFAULT; // 現在のソート方向
  this.arrayColumn = null; // 現在ソートを行っている列の優先順位
  this.arrayCol_Last = new Array(); // 最後にソートした列
} // classSortTable //

var g_cSortTable = new classSortTable();


// ----- public な関数として扱う -----


//
// ソートボタン
//
//    @param strId_table 対象 table の id
//    @param arrayColumn ソート条件とする列(第一、第二…とソートする優先順に配列で指定)
//
classSortTable.prototype.Button_Sort = function(strId_table, arrayColumn) {
  var class_pointer = this;

  class_pointer.boolExec = false;
  var objInterval = setInterval(
    function() {
      clearInterval(objInterval);
      (function() {
        if (class_pointer.boolExec) return;
        class_pointer.boolExec = true;

        class_pointer._Sort_Table(strId_table, arrayColumn);
      })();
    }, // function //
    200
  );
}; // classSortTable.prototype.Button_Sort //

//
// 行の比較
//
//    @param  elmTr_a 比較対象の行 A
//    @param  elmTr_b 比較対象の行 B
//    @return         比較結果
//
classSortTable.prototype.Compare = function(elmTr_a, elmTr_b) {
  var arrayColumn = this.arrayColumn; // 優先順位
  var numOrder = this.numOrder; // ソート方向
  var numResult = 0; // 比較結果

  var val_a = null; // 行 A のセルの値
  var val_b = null; // 行 B のセルの値

  var numLen_arrayColumn = arrayColumn.length;
  for (var i = 0; i < numLen_arrayColumn && numResult == 0; i++) {
    var alias_arrayColumn = arrayColumn[i];

    if (typeof(elmTr_a.textContent) != "undefined") {
      val_a = elmTr_a.childNodes[alias_arrayColumn].textContent;
      val_b = elmTr_b.childNodes[alias_arrayColumn].textContent;
    } else {
      val_a = elmTr_a.childNodes[alias_arrayColumn].innerText;
      val_b = elmTr_b.childNodes[alias_arrayColumn].innerText;
    } // if //

    if ((!isNaN(val_a) || val_a.length < 1) && (!isNaN(val_b) || val_b.length < 1)) {
      // セルが両方とも“数字か空白”なら数値としてソート
      // Number.NEGATIVE_INFINITY を空白の代替とする

      val_a = (val_a.length > 0) ? Number(val_a) : Number.NEGATIVE_INFINITY;
      val_b = (val_b.length > 0) ? Number(val_b) : Number.NEGATIVE_INFINITY;
    } // if //

    if (val_a < val_b) {
      numResult = (numOrder == -1) ? 1 : -1;
    } else if (val_a > val_b) {
      numResult = (numOrder != -1) ? 1 : -1;
    } // if //
  } // for //

  return numResult;
}; // classSortTable.prototype.Compare //


// ----- private な関数として扱う -----


//
//    ソート
//
//    @param strId_table 対象 table の id
//    @param arrayColumn ソート条件とする列(第一、第二…とソートする優先順に配列で指定)
//
classSortTable.prototype._Sort_Table = function(strId_table, arrayColumn) {
  // 対象外ブラウザのチェック
  if (!document.getElementById || !document.removeChild) {
    alert(this.MES_ALERT);
    return;
  } // if //

  var elmTable = document.getElementById(strId_table);
  var elmTbody = elmTable.getElementsByTagName("tbody").item(0);
  var elmTr = elmTbody.getElementsByTagName("tr");
  var arrayTr = new Array();

  // 現在の内容を取得
  var numLen_tr = elmTr.length;
  for (var i = 0; i < numLen_tr; i++) {
    var alias_tr = elmTr[i];
    var alias_childNodes = alias_tr.childNodes;
    for (var j = 0; j < alias_childNodes.length; j++) {
      if (alias_childNodes[j].nodeType != 1) {
        // 要素以外を取り除く
        alias_tr.removeChild(alias_childNodes[j]);
        j--;
      } // if //
    } // for //

    arrayTr[i] = alias_tr.cloneNode(true);
  } // for //

  var alias_arrayCol_Last = this.arrayCol_Last;
  if (alias_arrayCol_Last[strId_table] == null) alias_arrayCol_Last[strId_table] = -1;

  // 同じ列のソートは、ソート方向を反転
  this.numOrder = (alias_arrayCol_Last[strId_table] == arrayColumn[0]) ? this.numOrder * -1 : this.ORDER_DEFAULT;

  this.arrayColumn = arrayColumn;
  alias_arrayCol_Last[strId_table] = arrayColumn[0];

  var class_pointer = this;
  arrayTr.sort(function(elm_a, elm_b) {
    return class_pointer.Compare(elm_a, elm_b);
  });

  // ソート結果
  var elmTbody_result = elmTbody.cloneNode(false);
  var numLen_tr = arrayTr.length;
  for (i = 0; i < numLen_tr; i++) {
    elmTbody_result.appendChild(arrayTr[i]);
  } // for //
  elmTable.removeChild(elmTbody)
  elmTable.appendChild(elmTbody_result);
}; // classSortTable.prototype._Sort_Table //



//======================================================================
// 開発画面用
//======================================================================
// 開発、観光画面
function Navi(position, img, title, pos, text, exp) {

    var StyElm = document.getElementById("NaviView");
    StyElm.style.visibility = "visible";

    var posx = pos.indexOf(",");
    var posy = pos.indexOf(")");
    var x = pos.substring(1, posx);
    var winEvent = windowEvent();

    if (position == 1) {
        // right
        StyElm.style.marginLeft = (x - 19) * 32 + 478;
        StyElm.style.top = document.body.scrollTop + winEvent.clientY + 150;
    } else {
        // left
        StyElm.style.marginLeft = (x - 19) * 32 + 668;
        StyElm.style.top = document.body.scrollTop + winEvent.clientY + 150;
    }

    StyElm.innerHTML = "<table><tr><td class='M'><img class='NaviImg' src=" + img + "></td><td class='M'><div class='NaviTitle'>" + title + " " + pos + "<\/div><div class='NaviText'>" + text.replace("\n", "<br>") + "</div></td></tr></table>";
    if (exp) {
        StyElm.innerHTML += "<div class='NaviText'>" + eval(exp) + "<\/div>";
    }
}

function NaviClose() {
    var StyElm = document.getElementById("NaviView");
    StyElm.style.visibility = "hidden";
}

function windowEvent() {
    if (window.event) return window.event;
    for (var a = arguments.callee.caller; a;) {
        var b = a.arguments[0];
        if (b && b.constructor == MouseEvent) return b;
        a = a.caller
    }
    return null
};
