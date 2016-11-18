class MapDevelopment {
	private w;
	private p; // = $defaultTarget;
	private str;
	private g :any[]; // = [$com_max];
	private k1 :any[]; // = [$com_max];
	private k2 :any[]; // = [$com_max];
	private tmpCommand = [[[0,0,0,0,0]], [[0,0,0,0,0]]];
	private command :any[]; // = [$set_com];
	private listCommand :any[]; // = [$set_listcom];

	private islName :any[]; // = [$set_island];
	private listShip :any[]; // = [$set_ships];
	private listSatellite :any[]; // = [$set_eisei];

	private mx;
	private my;

	private php_com_count :number;
	private php_commandMax :number;
	private php_commands :any;
	private php_costs :any;
	private php_init :any;

	constructor(args :any) {

		for (let val in args) {
			this[val] = args[val];
		}


		for(let i = 0; i < this.command.length; i++) {
			for(let s = 0; s < this.php_com_count; s++) {
				let comlist2 = this.listCommand[s];
				for(let j = 0; j < comlist2.length; j++) {
					if(this.command[i][0] == comlist2[j][0]) {
						this.g[i] = comlist2[j][1];
					}
				}
			}
		}
		SelectList('');
		outp();
		this.str = plchg();
		this.str = '<div style="color:#00c">■ 送信済み ■<\\/div>' + this.str;
		disp(this.str, "");
		document.onmousemove = Mmove;
		// if(document.layers) {
		//  //document.captureEvents(Event.MOUSEMOVE | Event.MOUSEUP);
		//  document.addEventListener("DOMContentLoaded", Event.MOUSEMOVE | Event.MOUSEUP, false);
		// }
		document.onmouseup = Mup;
		document.onmousemove = Mmove;
		document.onkeydown = Kdown;
		document.ch_numForm.AMOUNT.options.length = 100;
		for(let i = 0; i < document.ch_numForm.AMOUNT.options.length; i++){
			document.ch_numForm.AMOUNT.options[i].value = i;
			document.ch_numForm.AMOUNT.options[i].text = i;
		}
		document.InputPlan.SENDPROJECT.disabled = true;
		ns(0);
	}

	cmdInput(theForm, x, k, z) {
		let numb = theForm.NUMBER.options[theForm.NUMBER.selectedIndex].value;
		let comd = theForm.COMMAND.options[theForm.COMMAND.selectedIndex].value;
		let pt_x = theForm.POINTX.options[theForm.POINTX.selectedIndex].value;
		let pt_y = theForm.POINTY.options[theForm.POINTY.selectedIndex].value;
		let amnt = theForm.AMOUNT.options[theForm.AMOUNT.selectedIndex].value;
		let targ = theForm.TARGETID.options[theForm.TARGETID.selectedIndex].value;

		if(x === 6){
			comd = k; menuclose();
		}

		let newNs = numb;
		if (x === 1 || x === 2 || x === 6){
			if(x === 6) comd = k;
			if(x !== 2) {
				for(let i = this.php_commandMax - 1; i > numb; i--) {
					this.command[i] = this.command[i-1];
					this.g[i] = this.g[i-1];
				}
			}
			for(let s = 0; s < this.php_com_count ;s++) {
				for(let i = 0, comlist2 = this.listCommand[s]; i < comlist2.length; i++){
					if(comlist2[i][0] == comd){
						this.g[numb] = comlist2[i][1];
						break;
					}
				}
			}
			this.command[numb] = [comd, pt_x, pt_y, amnt, targ];
			newNs++;
			// menuclose();
		} else if(x === 3) {
			let num = (k)? k-1: numb;
			for(let i = Math.floor(num); i < this.php_commandMax - 1; i++) {
				this.command[i] = this.command[i + 1];
				g[i] = g[i+1];
			}
			this.command[this.php_commandMax - 1] = [81, 0, 0, 0, 0];
			this.g[this.php_commandMax - 1] = '資金繰り';
		} else if(x === 4) {
			let i = Math.floor(numb);
			if (i === 0) return true;
			i = Math.floor(numb);
			this.tmpCommand[0][i] = this.command[i];
			this.tmpCommand[1][i] = this.command[i-1];
			this.command[i-1] = this.tmpCommand[0][i];
			this.command[i]   = this.tmpCommand[1][i];

			this.k1[i] = this.g[i];
			this.k2[i] = this.g[i-1];
			this.g[i] = this.k2[i];
			this.g[i-1] = this.k1[i];
			ns(--i);
			this.str = plchg();
			this.str = '<div style="color:#c7243a;font-weight:bold;">■ 未送信 ■<\\/div>' + this.str;
			disp(this.str,"white");
			outp();
			newNs = i+1;
		} else if(x === 5) {
			let i = Math.floor(numb);
			if (i === this.php_commandMax - 1) return true;
			this.tmpCommand[0][i] = this.command[i];
			this.tmpCommand[1][i] = this.command[i+1];
			this.command[i+1] = this.tmpCommand[0][i];
			this.command[i]   = this.tmpCommand[1][i];
			this.k1[i] = this.g[i];
			this.k2[i] = this.g[i+1];
			this.g[i+1] = this.k1[i];
			this.g[i]   = this.k2[i];
			newNs = i+1;
		}else if(x === 7){
			// 移動
			let _cmd = this.command[k];
			let _g = this.g[k];
			let i = k;
			if(z > k) {
				// 上から下へ
				for(;i < z-1; i++) {
					this.command[i] = this.command[i+1];
					this.g[i] = this.g[i+1];
				}
			} else {
				// 下から上へ
				for(;i > z; i--) {
					this.command[i] = this.command[i-1];
					this.g[i] = this.g[i-1];
				}
			}
			this.command[i] = _cmd;
			this.g[i] = _g;
			newNs = i+1;
		}else if(x == 8){
			this.command[numb][3] = k;
		}
		this.str = plchg();
		this.str = '<div style="color:#c7243a;font-weight:bold;">■ 未送信 ■<\\/div>' + this.str;
		disp(this.str, "");
		outp();
		theForm.SENDPROJECT.disabled = false;
		ns(newNs);

		return true;
	}

	plchg() {
		let strn1 = "";
		let strn2 = "";
		let arg = "";
		for(let i = 0; i < this.php_commandMax; i++) {
			let _cmd = this.command[i];
			let kind = '{$init->tagComName_}' + this.g[i] + '{$init->_tagComName}';
			let x = _cmd[1];
			let y = _cmd[2];
			let targ = _cmd[4];
			let point = '{$init->tagName_}' + "(" + x + "," + y + ")" + '{$init->_tagName}';

			for(let j = 0, islLen = this.islName.length; j < islLen; j++) {
				if(targ == this.islName[j][0]){
					targ = '{$init->tagName_}' + this.islName[j][1] + "島" + '{$init->_tagName}';
				}
			}

			switch(c[0]) {
				// ▼ミサイル撃ち止め、資金繰り、島の放棄
				case this.php_commands.missileSM:
				case this.php_commands.doNothing:
				case this.php_commands.giveup:
					strn2 = kind;
					break;
				// ▼ミサイル関連
				case this.php_commands.missileNM:
				case this.php_commands.missilePP:
				case this.php_commands.missileST:
				case this.php_commands.missileBT:
				case this.php_commands.missileSP:
				case this.php_commands.missileLD:
				case this.php_commands.missileLU:
					arg = (c[3]===0)? "（無制限）": "（" + c[3] + "発）";
					strn2 = targ + point + "へ" + kind + arg;
					break;
				// ▼怪獣派遣・怪獣転嫁
				case this.php_commands.sendMonster:
				case this.php_commands.sendSleeper:
					strn2 = targ + "へ" + kind;
					break;
				// ▼食料輸出
				case this.php_commands.sellFood:
					arg = "（" + Math.max(1,c[3])*100 + this.php_init.unitFood + "）";
					strn2 = kind + arg;
					break;
				// ▼木材輸出
				case this.php_commands.sellWood:
					arg = "（" + Math.max(1,c[3])*10 + this.php_init.unitWood + "）";
					strn2 = kind + arg;
					break;
				// ▼資金援助
				case this.php_commands.aidMoney:
					arg = "（" + Math.max(1,c[3])*this.php_costs.aidMoney + this.php_init.unitMoney + "）";
					strn2 = targ + "へ" + kind + arg;
					break;
				// ▼食料援助
				case this.php_commands.aidFood:
					arg = "（" + Math.max(1,c[3])*100 + this.php_init.unitFood + "）";
					strn2 = targ + "へ" + kind + arg;
					break;
				// ▼掘削
				case this.php_commands.excavate:
					arg = "（予算：" + Math.max(1,c[3])*this.php_costs.excavate + this.php_init.unitMoney + "）";
					strn2 = point + "で" + kind + arg;
					break;
				// ▼宝くじ購入
				case this.php_commands.buyLot:
					arg = "（予算：" + Math.max(1,Math.min(c[3],30))*this.php_costs.buyLot + this.php_init.unitMoney + "）";
					strn2 = kind + arg;
					break;
				// ▼防衛施設
				case this.php_commands.barrierLand:
						arg = "（耐久力" + Math.max(1,Math.min(c[3],this.php_init.barrierLandHP)) + "）";
						strn2 = point + "で" + kind + arg;
					break;
				// ▼海底防衛施設
				case this.php_commands.barrierOffshore:
					arg = "（耐久力" + Math.max(1,Math.min(c[3],this.php_init.barrierOffshoreHP)) + "）";
					strn2 = point + "で" + kind + arg;
					break;
				// ▼船の破棄
				case this.php_commands.shipReject:
					strn2 = point + "で" + kind;
					break;
				// ▼倉庫建設（資金）
				case this.php_commands.repositoryMoney:
					arg = "（" + (()=>{return (c[3]===0)?"（セキュリティ強化）":c[3]*1000;}) + this.php_init.unitMoney + "）";
					strn2 = point + "で" + kind + arg;
					break;
				// ▼倉庫建設（食料）
				case this.php_commands.repositoryFood:
					arg = "（" + (()=>{return (c[3]===0)?"（セキュリティ強化）":c[3]*1000;}) + this.php_init.unitMoney + "）";
					strn2 = point + "で" + kind + arg;
					break;
				// ▼倉庫引き出し
				case this.php_commands.repositoryWithdraw:
				if(c[3] == 0) c[3] = 1;
				arg = c[3] * 1000;
				arg = "（" + arg + "{$init->unitMoney} or " + arg + "{$init->unitFood}）";
				strn2 = point + "で" + kind + arg;

					break;
				case this.php_commands.:
					break;
				case this.php_commands.:
					break;
				case this.php_commands.:
					break;
				case this.php_commands.:
					break;
				case this.php_commands.:
					break;
				default:
					// code...
					break;
			}

			if(c[0] == this.php_commands.missileSM || c[0] == this.php_commands.doNothing || c[0] == this.php_commands.giveup){
			} else if(c[0] == $init->comFarm || // 農場、海底農場、工場、商業ビル、採掘場整備、発電所、僕の引越し
				c[0] == $init->comSfarm ||
				c[0] == $init->comFactory ||
				c[0] == $init->comCommerce ||
				c[0] == $init->comMountain ||
				c[0] == $init->comHatuden ||
				c[0] == $init->comBoku) {
				if(c[3] != 0){
					arg = "（" + c[3] + "回）";
					strn2 = point + "で" + kind + arg;
				}else{
					strn2 = point + "で" + kind;
				}
			} else if(c[0] == $init->comPropaganda || // 誘致活動
				c[0] == $init->comOffense || // 強化
				c[0] == $init->comDefense ||
				c[0] == $init->comPractice) {
				if(c[3] != 0){
					arg = "（" + c[3] + "回）";
					strn2 = kind + arg;
				}else{
					strn2 = kind;
				}
			} else if(c[0] == $init->comPlaygame) { // 試合
				strn2 = targ + "と" + kind;
			} else if(c[0] == $init->comMakeShip){ // 造船
				if(c[3] >= $init->shipKind) {
					c[3] = $init->shipKind - 1;
				}
				arg = c[3];
				strn2 = point + "で" + kind + " (" + shiplist[arg] + ")";
			} else if(c[0] == $init->comSendShip){ // 船派遣
				strn2 = targ + "へ" + point + "の" + kind;
			} else if(c[0] == $init->comReturnShip){ // 船帰還
				strn2 = targ + point + "の" + kind;
			} else if(c[0] == $init->comEisei){ // 人工衛星打ち上げ
				if(c[3] >= $init->EiseiNumber) {
					c[3] = 0;
				}
				arg = c[3];
				strn2 = '{$init->tagComName_}' + eiseilist[arg] + "打ち上げ" + '{$init->_tagComName}';
			} else if(c[0] == $init->comEiseimente){ // 人工衛星修復
				if(c[3] >= $init->EiseiNumber) {
					c[3] = 0;
				}
				arg = c[3];
				strn2 = '{$init->tagComName_}' + eiseilist[arg] + "修復" + '{$init->_tagComName}';
			} else if(c[0] == $init->comEiseiAtt){ // 人工衛星破壊
				if(c[3] >= $init->EiseiNumber) {
					c[3] = 0;
				}
				arg = c[3];
				strn2 = targ + "へ" + '{$init->tagComName_}' + eiseilist[arg] + "破壊砲発射" + '{$init->_tagComName}';
			} else if(c[0] == $init->comEiseiLzr) { // 衛星レーザー
				strn2 = targ + point + "へ" + kind;
			}else{
				strn2 = point + "で" + kind;
			}
			tmpnum = '';
			if(i < 9){ tmpnum = '0'; }
			strn1 +=
				'<div id="com_'+i+'" '+
					'onmouseover="mc_over('+i+');return false;" '+
					'><a HREF="javascript:void(0);" onclick="ns('+i+')" onkeypress="ns('+i+')" '+
					'onmousedown="return comListMove('+i+');" '+'ondblclick="chNum('+c[3]+');return false;" '+
					'><nobr>'+
					tmpnum+(i+1)+':'+
					strn2+'<\\/nobr><\\/a><\\/div>\\n';
		}

		return strn1;
	}

	disp(str, bgColor? :string) {
		if(str==null) str = "";
		if(bgColor===undefined) bgColor = '#fff';
		LayWrite('LINKMSG1', str);
		SetBG('plan', bgColor);
	}

	outp() {
		comary = "";

		for(k = 0; k < command.length; k++){
			comary = comary + command[k][0]
				+ " " + command[k][1]
				+ " " + command[k][2]
				+ " " + command[k][3]
				+ " " + command[k][4]
				+ " " ;
		}
		document.InputPlan.COMARY.value = comary;
	}

	ps(x, y) {
		document.InputPlan.POINTX.options[x].selected = true;
		document.InputPlan.POINTY.options[y].selected = true;
		if(!(document.InputPlan.MENUOPEN.checked)) {
			moveLAYER("menu", mx+10, my-50);
		}
		NaviClose();
		return true;
	}

	ns(x) {
		if (x !== this.php_commandMax) document.InputPlan.NUMBER.options[x].selected = true;
		return true;
	}

	set_com(x, y, land) {
		com_str = land + " ";
		for(i = 0; i < $init->commandMax; i++) {
			c = command[i];
			x2 = c[1];
			y2 = c[2];
			if(x == x2 && y == y2 && c[0] < 30){
				com_str += "[" + (i + 1) +"]" ;
				kind = g[i];
				if(c[0] == $init->comDestroy){
					if(c[3] == 0){
						com_str += kind;
					} else {
						arg = c[3] * 200;
						arg = "（予\算" + arg + "{$init->unitMoney}）";
						com_str += kind + arg;
					}
				} else if(c[0] == $init->comLot){
					if(c[3] == 0) c[3] = 1;
					if(c[3] > 30) c[3] = 30;
						arg = c[3] * 300;
						arg = "（予\算" + arg + "{$init->unitMoney}）";
						com_str += kind + arg;
				} else if(c[0] == $init->comFarm ||
					c[0] == $init->comSfarm ||
					c[0] == $init->comFactory ||
					c[0] == $init->comCommerce ||
					c[0] == $init->comMountain ||
					c[0] == $init->comHatuden ||
					c[0] == $init->comBoku ||
					c[0] == $init->comPropaganda ||
					c[0] == $init->comOffense ||
					c[0] == $init->comDefense ||
					c[0] == $init->comPractice) {
					if(c[3] != 0){
						arg = "（" + c[3] + "回）";
						com_str += kind + arg;
					} else {
						com_str += kind;
					}
				} else {
					com_str += kind;
				}
				com_str += " ";
			}
		}
		document.InputPlan.COMSTATUS.value= com_str;
	}

	SelectList(theForm) {
		var u, selected_ok;
		if(!theForm) { s = '' }
		else { s = theForm.menu.options[theForm.menu.selectedIndex].value; }
		if(s == ''){
			u = 0; selected_ok = 0;
			document.InputPlan.COMMAND.options.length = $All_listCom;
			for (i=0; i<this.listCommand.length; i++) {
				var command = this.listCommand[i];
				for (a=0; a<command.length; a++) {
					comName = command[a][1] + "(" + command[a][2] + ")";
					document.InputPlan.COMMAND.options[u].value = command[a][0];
					document.InputPlan.COMMAND.options[u].text = comName;
					if(command[a][0] == $default_Kind){
						document.InputPlan.COMMAND.options[u].selected = true;
						selected_ok = 1;
					}
					u++;
				}
			}
			if(selected_ok == 0)
				document.InputPlan.COMMAND.selectedIndex = 0;
		} else {
			var command = this.listCommand[s];
			document.InputPlan.COMMAND.options.length = command.length;
			for (i=0; i<command.length; i++) {
				comName = command[i][1] + "(" + command[i][2] + ")";
				document.InputPlan.COMMAND.options[i].value = command[i][0];
				document.InputPlan.COMMAND.options[i].text = comName;
				if(command[i][0] == $default_Kind){
					document.InputPlan.COMMAND.options[i].selected = true;
					selected_ok = 1;
				}
			}
			if(selected_ok == 0) {
				document.InputPlan.COMMAND.selectedIndex = 0;
			}
		}
	}

	moveLAYER(layName,x,y){
		let el = <HTMLElement>document.getElementById(layName);
		el.style.left = x + "px";
		el.style.top  = y + "px";
	}

	menuclose() {
		moveLAYER("menu", -500, -500);
	}

	Mmove(e){
		mx = e.pageX;
		my = e.pageY;
		return moveLay.move();
	}

	LayWrite(layName:string, str:string) {
		document.getElementById(layName).innerHTML = str;
	}

	setBGColor(layName:string, bgColor:string) {
		document.getElementById(layName).style.backgroundColor = bgColor;
	}

	selCommand(num) {
		let oldNum=0;
		document.getElementById('com_'+oldNum).style.backgroundColor = '';
		document.getElementById('com_'+num).style.backgroundColor = '#ffa';
		oldNum = num;
	}

}

/* コマンド ドラッグ＆ドロップ用追加スクリプト */
var moveLay = new MoveFalse();
var newLnum = -2;
var Mcommand = false;

function Mup() {
	moveLay.up();
	moveLay = new MoveFalse();
}

function setBorder(num, color) {
	if(color.length == 4) {
		document.getElementById('com_'+num).style.borderTop = ' 1px solid '+color;
	} else {
		document.getElementById('com_'+num).style.border = '0px';
	}
}

function mc_out() {
	if(Mcommand && newLnum >= 0) {
		setBorder(newLnum, '');
		newLnum = -1;
	}
}

function mc_over(num) {
	if(Mcommand) {
		if(newLnum >= 0) setBorder(newLnum, '');
		newLnum = num;
		setBorder(newLnum, '#116'); // blue
	}
}

function comListMove(num) {
	moveLay = new MoveComList(num);
	return (document.layers) ? true : false;
}

function MoveFalse() {
	this.move = function() { }
	this.up = function() { }
}

function MoveComList(num) {
	var setLnum = num;
	Mcommand = true;
	LayWrite('mc_div', '<NOBR><strong>'+(num+1)+': '+g[num]+'</strong></NOBR>');
	this.move = function() {
		moveLAYER('mc_div', mx+10, my-30);
		return false;
	}
	this.up = function() {
		if(newLnum >= 0) {
			var com = command[setLnum];
			cominput(document.InputPlan,7,setLnum,newLnum);
		} else if(newLnum == -1) {
			cominput(document.InputPlan,3,setLnum+1);
		}
		mc_out();
		newLnum = -2;
		Mcommand = false;
		moveLAYER("mc_div",-50,-50);
	}
}

function showElement(layName) {
	var element = document.getElementById(layName).style;
	element.display = "block";
	element.visibility ='visible';
}

function hideElement(layName) {
	var element = document.getElementById(layName).style;
	element.display = "none";
}

function chNum(num) {
	document.ch_numForm.AMOUNT.options.length = 100;
	for(var i=0; i<document.ch_numForm.AMOUNT.options.length; i++){
		if(document.ch_numForm.AMOUNT.options[i].value == num){
			document.ch_numForm.AMOUNT.selectedIndex = i;
			document.ch_numForm.AMOUNT.options[i].selected = true;
			moveLAYER('ch_num', mx-10, my-60);
			showElement('ch_num');
			break;
		}
	}
}

function chNumDo() {
	var num = document.ch_numForm.AMOUNT.options[document.ch_numForm.AMOUNT.selectedIndex].value;
	cominput(document.InputPlan,8,num);
	hideElement('ch_num');
}

function Kdown(e){
	var c, el;
	var m = document.InputPlan.AMOUNT.selectedIndex;
	if(m > 9) {
		m = 0;
	}

	if (e.altKey || e.ctrlKey || e.shiftKey) {
		return;
	}
	c = e.which;
	el = new String(e.target.tagName);
	el = el.toUpperCase();
	if (el == "INPUT") {
		return;
	}

	c = String.fromCharCode(c);

	// 押されたキーに応じて計画番号を設定する
	switch (c) {
		case 'A': c = $init->comPrepare; break; // 整地
		case 'J': c = $init->comPrepare2; break; // 地ならし
		case 'U': c = $init->comReclaim; break; // 埋め立て
		case 'K': c = $init->comDestroy; break; // 掘削
		case 'B': c = $init->comSellTree; break; // 伐採
		case 'P': c = $init->comPlant; break; // 植林
		case 'N': c = $init->comFarm; break; // 農場整備
		case 'I': c = $init->comFactory; break; // 工場建設
		case 'S': c = $init->comMountain; break; // 採掘場整備
		case 'D': c = $init->comDbase; break; // 防衛施設建設
		case 'M': c = $init->comBase; break; // ミサイル基地建設
		case 'F': c = $init->comSbase; break; // 海底基地建設
		case '-': c = $init->comDoNothing; break; //INS 資金繰り
		case '.': cominput(InputPlan,3); return; //DEL 削除
		case'\b': //BS 一つ前削除
		var no = document.InputPlan.COMMAND.selectedIndex;
		if(no > 0) {
			document.InputPlan.COMMAND.selectedIndex = no - 1;
		}
		cominput(InputPlan,3);
		return;
		case '0':case'`': document.InputPlan.AMOUNT.selectedIndex = m*10+0; return;
		case '1':case'a': document.InputPlan.AMOUNT.selectedIndex = m*10+1; return;
		case '2':case'b': document.InputPlan.AMOUNT.selectedIndex = m*10+2; return;
		case '3':case'c': document.InputPlan.AMOUNT.selectedIndex = m*10+3; return;
		case '4':case'd': document.InputPlan.AMOUNT.selectedIndex = m*10+4; return;
		case '5':case'e': document.InputPlan.AMOUNT.selectedIndex = m*10+5; return;
		case '6':case'f': document.InputPlan.AMOUNT.selectedIndex = m*10+6; return;
		case '7':case'g': document.InputPlan.AMOUNT.selectedIndex = m*10+7; return;
		case '8':case'h': document.InputPlan.AMOUNT.selectedIndex = m*10+8; return;
		case '9':case'i': document.InputPlan.AMOUNT.selectedIndex = m*10+9; return;
		case 'Z':case'j': document.InputPlan.AMOUNT.selectedIndex = 0; return;
		default:
		// IE ではリロードのための F5 まで拾うので、ここに処理をいれてはいけない
		return;
	}
	cominput(document.InputPlan, 6, c);
}

	function setTarget(part){
		let p = part.options[part.selectedIndex].value;
	}

	function targetOpen() {
		let w = window.open("{$this_file}?target=" + p, "","width={$width},height={$height},scrollbars=1,resizable=1,toolbar=1,menubar=1,location=1,directories=0,status=1");
	}

