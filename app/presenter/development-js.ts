class MapDevelopment {
	private _msgs;
	private w;
	private p; // = $defaultTarget;

	private str;
	private g :string[]; // = [$com_max] => [0,0,0,0, ... 0]
	private k1 :string[]; // = [$com_max]
	private k2 :string[]; // = [$com_max]
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
	private php_All_listCom :number;
	private php_default_Kind :number;
	private php_commands :any;
	private php_costs :any;
	private php_init :any;

	public events;

	constructor() {

		let args = this.getPhpConfigs();
		for (let key in args) {
			this[key] = args[key];
		}
		this._msgs.sync.notyet  = `<div style="color:#c7243a;font-weight:bold;">■ 未送信 ■</div>`;
		this._msgs.sync.already = `<div style="color:#00c">■ 送信済 ■</div>`;


		// this.command内のコマンドIDとthis.listCommand内のコマンドID・名称対照
		// @return :string[] 計画中コマンド群の名称リスト
		for(let i = 0, len = this.command.length; i < len; i++) {
			for(let s = 0; s < this.php_com_count; s++) {
				let comlist2 = this.listCommand[s];
				for(let j = 0, len_j = comlist2.length; j < len_j; j++) {
					if(this.command[i][0] == comlist2[j][0]) {
						this.g[i] = comlist2[j][1];
					}
				}
			}
		}

		// コマンドリスト初期表示
		this.selectList();
		this.writeCmdArray();
		this.str = this.changePlan();
		this.str = this._msgs.sync.already + this.str;
		this.disp(this.str);

		this.events = new Events('plan');

		document.forms.ch_numForm.AMOUNT.length = 100;
		for(let i = 0; i < document.forms.ch_numForm.AMOUNT.length; i++){
			document.forms.ch_numForm.AMOUNT[i].value = i;
			document.forms.ch_numForm.AMOUNT[i].text = i;
		}
		document.forms.InputPlan.sendProj.disabled = true;
		this.numberSelect(0);
	}

	private getPhpConfigs(){
		let request = new XMLHttpRequest();
		let ret = {};
		request.open('GET', 'mapDevelopment.php', false);
		request.onload = function() {
			if (request.status >= 200 && request.status < 400) {
				// Success!
				ret = JSON.parse(request.responseText);
			} else {
				console.error(request.responseText);
				throw "We reached our target server, but it returned an error";
			}
		};
		request.onerror = function() {
			throw 'There was a connection error of some sort';
		};
		request.send();
		return ret;
	}

	/**
	 * => cominput()
	 * @param {HTMLFormElement} form [description]
	 * @param {number} x       Function's action selectoring number from 1 to 8.
	 * @param {number|undefined} k       Plan number
	 * @param {[type]} z       [description]
	 */
	cmdInput(form:HTMLFormElement, x:number, k?:number, z) {
		let numb:number = form.number[form.number.selectedIndex].value;
		let comd:number = form.COMMAND[form.COMMAND.selectedIndex].value;
		let pt_x:number = form.POINTX[form.POINTX.selectedIndex].value;
		let pt_y:number = form.POINTY[form.POINTY.selectedIndex].value;
		let amnt:number = form.AMOUNT[form.AMOUNT.selectedIndex].value;
		let targ:number = form.TARGETID[form.TARGETID.selectedIndex].value;


		let newNs = numb;
		if (x === 1 || x === 2 || x === 6){
			if(x === 6){
				comd = k;
				menuclose();
			}
			if(x !== 2) {
				for(let i = this.php_commandMax - 1; i > numb; i--) {
					this.command[i] = this.command[i-1];
					this.g[i] = this.g[i-1];
				}
			}
			for(let s = 0; s < this.php_com_count; s++) {
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
			this.str = this._msgs.sync.notyet + this.str;
			disp(this.str);
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
		this.str = this._msgs.sync.notyet + this.changePlan();
		this.disp(this.str);
		outp();
		form.sendProj.disabled = false;
		this.numberSelect(newNs);

		return true;
	}

	changePlan() {
		let returnVal = "";
		let cmdText = "";
		let arg = "";
		for(let i = 0; i < this.php_commandMax; i++) {
			let _cmd :any[] = this.command[i];
			let targ = _cmd[4];
			const kind = `<span class="command">${this.g[i]}</span>`;
			const point = `<span class="islName">(${_cmd[1]},${_cmd[2]})</span>`;

			for(let j = 0, islLen = this.islName.length; j < islLen; j++) {
				if(targ === this.islName[j][0]){
					targ = `<span class="islName">${this.islName[j][1]}島</span>`;
				}
			}

			switch(_cmd[0]) {
				// ▼ミサイル撃ち止め、資金繰り、島の放棄
				case this.php_commands.missileSM:
				case this.php_commands.doNothing:
				case this.php_commands.giveup:
					cmdText = kind;
					break;
				// ▼ミサイル関連
				case this.php_commands.missileNM:
				case this.php_commands.missilePP:
				case this.php_commands.missileST:
				case this.php_commands.missileBT:
				case this.php_commands.missileSP:
				case this.php_commands.missileLD:
				case this.php_commands.missileLU:
					arg = (_cmd[3]===0)? '（無制限）': '（' + _cmd[3] + '発）';
					cmdText = targ + point + "へ" + kind + arg;
					break;
				// ▼怪獣派遣・怪獣転嫁
				case this.php_commands.sendMonster:
				case this.php_commands.sendSleeper:
					cmdText = targ + "へ" + kind;
					break;
				// ▼食料輸出
				case this.php_commands.sellFood:
					arg = "（" + Math.max(1,_cmd[3])*100 + this.php_init.unitFood + "）";
					cmdText = kind + arg;
					break;
				// ▼木材輸出
				case this.php_commands.sellWood:
					arg = "（" + Math.max(1,_cmd[3])*10 + this.php_init.unitWood + "）";
					cmdText = kind + arg;
					break;
				// ▼資金援助
				case this.php_commands.aidMoney:
					arg = "（" + Math.max(1,_cmd[3])*this.php_costs.aidMoney + this.php_init.unitMoney + "）";
					cmdText = targ + "へ" + kind + arg;
					break;
				// ▼食料援助
				case this.php_commands.aidFood:
					arg = "（" + Math.max(1,_cmd[3])*100 + this.php_init.unitFood + "）";
					cmdText = targ + "へ" + kind + arg;
					break;
				// ▼掘削
				case this.php_commands.excavate:
					arg = "（予算：" + Math.max(1,_cmd[3])*this.php_costs.excavate + this.php_init.unitMoney + "）";
					cmdText = point + "で" + kind + arg;
					break;
				// ▼宝くじ購入
				case this.php_commands.buyLot:
					arg = "（予算：" + Math.max(1,Math.min(_cmd[3],30))*this.php_costs.buyLot + this.php_init.unitMoney + "）";
					cmdText = kind + arg;
					break;
				// ▼防衛施設
				case this.php_commands.barrierLand:
						arg = "（耐久力" + Math.max(1,Math.min(_cmd[3],this.php_init.barrierLandHP)) + "）";
						cmdText = point + "で" + kind + arg;
					break;
				// ▼海底防衛施設
				case this.php_commands.barrierOffshore:
					arg = "（耐久力" + Math.max(1,Math.min(_cmd[3],this.php_init.barrierOffshoreHP)) + "）";
					cmdText = point + "で" + kind + arg;
					break;
				// ▼船の破棄
				case this.php_commands.shipReject:
					cmdText = point + "で" + kind;
					break;
				// ▼倉庫建設（資金）
				case this.php_commands.repositoryMoney:
					arg = (_cmd[3]===0)? "（セキュリティ強化）": ('（' + _cmd[3]*1000 + this.php_init.unitMoney + '）');
					cmdText = point + "で" + kind + arg;
					break;
				// ▼倉庫建設（食料）
				case this.php_commands.repositoryFood:
					arg = "（" + (()=>{return (_cmd[3]===0)?"（セキュリティ強化）":_cmd[3]*1000;}) + this.php_init.unitMoney + "）";
					cmdText = point + "で" + kind + arg;
					break;
				// ▼倉庫引き出し
				case this.php_commands.repositoryWithdraw:
					arg = "（" + Math.max(1,_cmd[3])*1000 + this.php_init.unitMoney + " or " + Math.max(1,_cmd[3])*1000 + this.php_init.unitFood + "）";
					cmdText = point + "で" + kind + arg;
					break;
				// ▼農場、海底農場、工場、商業ビル、採掘場整備、発電所、僕の引越し
				case this.php_commands.farm:
				case this.php_commands.farmOffshore:
				case this.php_commands.factory:
				case this.php_commands.commerce:
				case this.php_commands.mountain:
				case this.php_commands.dynamo:
				case this.php_commands.transport:
					arg = (_cmd[3] === 0)? '': "（" + _cmd[3] + "回）";
					cmdText = point + "で" + kind + arg;
					break;
				// ▼誘致活動・スポーツ強化
				case this.php_commands.propaganda:
				case this.php_commands.sportsOffence:
				case this.php_commands.sportsDefence:
				case this.php_commands.sportsPractice:
					arg = (_cmd[3] === 0)? '': "（" + _cmd[3] + "回）";
					cmdText = kind + arg;
					break;
				// ▼スポーツ試合
				case this.php_commands.sportsGame:
					cmdText = targ + "と" + kind;
					break;
				// ▼造船
				case this.php_commands.shipMake:
					cmdText = point + "で" + kind + " (" + this.php_init.shiplist[Math.min(_cmd[3], this.php_init.shipKind-1)] + "）";
					break;
				// ▼船派遣
				case this.php_commands.shipSend:
					cmdText = targ + "へ" + point + "の" + kind;
					break;
				// ▼船帰還
				case this.php_commands.shipReturn:
					cmdText = targ + point + "の" + kind;
					break;
				// ▼人工衛星打ち上げ
				case this.php_commands.eisei:
					if(_cmd[3] >= $init->EiseiNumber) {
						_cmd[3] = 0;
					}
					arg = _cmd[3];
					cmdText = this.php_init.eiseilist[arg] + "打ち上げ";
					break;
				// ▼人工衛星修復
				case this.php_commands.eiseiMente:
					if(_cmd[3] >= $init->EiseiNumber) {
						_cmd[3] = 0;
					}
					arg = _cmd[3];
					cmdText = '{$init->tagComName_}' + eiseilist[arg] + "修復" + '{$init->_tagComName}';
					break;
				// ▼人工衛星破壊
				case this.php_commands.eiseiDestroy:
					if(_cmd[3] >= $init->EiseiNumber) {
						_cmd[3] = 0;
					}
					arg = _cmd[3];
					cmdText = targ + "へ" + '{$init->tagComName_}' + eiseilist[arg] + "破壊砲発射" + '{$init->_tagComName}';
					break;
				// 衛星レーザー
				case this.php_commands.eiseiLayser:
					cmdText = targ + point + "へ" + kind;
					break;
				default:
					cmdText = point + "で" + kind;
					break;
			}

			const fixed_i = (i < 9)? '0'+(i+1): i+1;
			returnVal += `<div id="com_${i}" onmouseover="mc_over(${i});return false;"><a href="javascript:void(0);" onclick="ns(${i})" onkeypress="ns(${i})" onmousedown="return comListMove(${i});" ondblclick="chNum(${_cmd[3]})">${fixed_i}: ${cmdText}</a></div>\n`;
		}

		return returnVal;
	}

	disp(str, bgColor?:string) {
		str = str || '';
		bgColor = bgColor || '#fff';
		Util.layerWrite('IsSynced', str);
		Util.setBGColor('plan', bgColor);
	}

	writeCmdArray() {
		let cmdArray = '';

		for(let k = 0, len = this.command.length; k < len; k++){
			cmdArray += this.command[k][0] + " " + this.command[k][1]
				+ " " + this.command[k][2] + " " + this.command[k][3]
				+ " " + this.command[k][4] + " ";
		}
		document.forms.InputPlan.COMARY.value = cmdArray;
	}

	// => ps()
	pointSelect(x, y) {
		document.forms.InputPlan.POINTX[x].selected = true;
		document.forms.InputPlan.POINTY[y].selected = true;
		if(!(document.forms.InputPlan.MENUOPEN.checked)) {
			moveLAYER("menu", mx+10, my-50);
		}
		NaviClose();
		return true;
	}

	// => ns()
	numberSelect(x) {
		if (x !== this.php_commandMax) document.forms.InputPlan.number[x].selected = true;
		return true;
	}

	set_com(x, y, land) {
		let com_str = land + " ";
		for(let i = 0; i < this.php_commandMax; i++) {
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
						arg = "（予算" + arg + "{$init->unitMoney}）";
						com_str += kind + arg;
					}
				} else if(c[0] == $init->comLot){
					if(c[3] == 0) c[3] = 1;
					if(c[3] > 30) c[3] = 30;
						arg = c[3] * 300;
						arg = "（予算" + arg + "{$init->unitMoney}）";
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
		document.forms.InputPlan.COMSTATUS.value= com_str;
	}

	// => SelectList()
	selectList(form?) {
		let isSelect :boolean = false;
		let s = (!form)? '': form.menu.value;
		if(s === '') {
			let u :number = 0;
			document.forms.InputPlan.commands.length = this.php_All_listCom;
			for (let i = 0, len = this.listCommand.length; i < len; i++) {
				let command = this.listCommand[i];
				for (let j = 0, len_command = command.length; j < len_command; j++) {
					let comName = command[j][1] + "(" + command[j][2] + ")";
					document.forms.InputPlan.commands[u].value = command[j][0];
					document.forms.InputPlan.commands[u].text  = comName;
					if(command[j][0] === this.php_default_Kind){
						document.forms.InputPlan.commands[u].selected = true;
						isSelect = true;
					}
					u++;
				}
			}
		} else {
			let command = this.listCommand[s];
			document.forms.InputPlan.commands.length = command.length;
			for (let i = 0, len = command.length; i < len; i++) {
				let cmdName = command[i][1] + "(" + command[i][2] + ")";
				document.forms.InputPlan.commands[i].value = command[i][0];
				document.forms.InputPlan.commands[i].text  = cmdName;
				if(command[i][0] === this.php_default_Kind){
					document.forms.InputPlan.commands[i].selected = true;
					isSelect = true;
				}
			}
		}
		if(!isSelect) document.forms.InputPlan.commands.selectedIndex = 0;
	}

	offsetLayer(layerId:string, x:number, y:number){
		let el = <HTMLElement>document.getElementById(layerId);
		el.style.left = x + "px";
		el.style.top  = y + "px";
	}

	menuClose() {
		Util.hideElement('menu');
	}


	selCommand(num) {
		let oldNum = 0;
		document.getElementById('com_'+oldNum).style.backgroundColor = '';
		document.getElementById('com_'+num).style.backgroundColor = '#ffa';
		oldNum = num;
	}

	chNum(num) {
		document.forms.ch_numForm.AMOUNT.length = 100;
		for(let i=0, len=document.forms.ch_numForm.AMOUNT.length; i<len; i++){
			if(document.forms.ch_numForm.AMOUNT[i].value === num){
				document.forms.ch_numForm.AMOUNT.selectedIndex = i;
				document.forms.ch_numForm.AMOUNT[i].selected = true;
				this.offsetLayer('ch_num', this.mx-10, this.my-60);
				Util.showElement('ch_num');
				break;
			}
		}
		return false;
	}

	chNumDo() {
		let num = document.forms.ch_numForm.AMOUNT.value;
		cominput(document.forms.InputPlan, 8, num);
		Util.hideElement('ch_num');
	}

}

/**
 * イベント管理
 */
class Events {
	private moveLay = new MoveFalse();
	private newLineIndex :number = -2;
	private Mcommand :boolean = false;
	private lineColor :string = '#116'; // blue

	constructor(targId:string) {
		let el = document.getElementById(targId);
		el.addEventListener('mouseover', this.evMouseOver, false);
		el.addEventListener('mouseup', this.evMouseUp, false);
		document.addEventListener('keydown', this.evKeydown, false);
	}
	Mmove(e){
		this.mx = e.pageX;
		this.my = e.pageY;
		return moveLay.move();
	}
	moveUp() {
		this.moveLay.up();
		this.moveLay = new MoveFalse();
	}
	setBorder(num:number, colorCode?:string) {
		if(typeof colorCode !== undefined) {
			document.getElementById('com_'+num).style.borderTop = '1px solid '+colorCode;
		} else {
			document.getElementById('com_'+num).style.border = '0';
		}
	}
	mc_out() {
		if(this.Mcommand && this.newLineIndex >= 0) {
			this.setBorder(this.newLineIndex);
			this.newLineIndex = -1;
		}
	}
	mc_over(num:number) {
		if(this.Mcommand) {
			if(this.newLineIndex >= 0) this.setBorder(this.newLineIndex);
			this.newLineIndex = num;
			this.setBorder(this.newLineIndex, this.lineColor);
		}
	}
	comListMove(num:number) {
		this.moveLay = new MoveComList(num);
		return (document.layers)? true: false;
	}
	MoveFalse() {
		this.move = function() { }
		this.up = function() { }
	}
	MoveComList(num) {
		let setLineNum = num;
		this.Mcommand = true;
		layerWrite('mc_div', '<span>'+(num+1)+': '+g[num]+'<span>');
		const move = ()=>{
			offsetLayer('mc_div', this.mx+10, this.my-30);
			return false
		};
		const up = ()=>{
			if(this.newLineIndex >= 0) {
				let com = command[setLineNum];
				cominput(document.forms.InputPlan, 7, this.setLineNum, this.newLineIndex);
			} else if(this.newLineIndex === -1) {
				cominput(document.forms.InputPlan, 3, setLineNum+1);
			}
			mc_out();
			this.newLineIndex = -2;
			this.Mcommand = false;
			offsetLayer('mc_div', -50, -50);
		};
		return {
			up: up,
			move: move
		};
	}
	evKeydown(e:KeyboardEvent){
		if (e.defaultPrevented) return;
		if (e.target.tagName === 'input') return;
		if (e.altKey || e.ctrlKey || e.shiftKey) return;

		let char = e.key.toLowerCase();

		// 押されたキーに応じて計画を設定する
		switch (char) {
			case 'a': // ▼整地
				char = this.php_command.Prepare; break;
			case 'j': // ▼地ならし
				char = this.php_command.Prepare2; break;
			case 'u': // ▼埋め立て
				char = this.php_command.Reclaim; break;
			case 'k': // ▼掘削
				char = this.php_command.Destroy; break;
			case 'b': // ▼伐採
				char = this.php_command.SellTree; break;
			case 'p': // ▼植林
				char = this.php_command.Plant; break;
			case 'n': // ▼農場整備
				char = this.php_command.Farm; break;
			case 'i': // ▼工場建設
				char = this.php_command.Factory; break;
			case 's': // ▼採掘場整備
				char = this.php_command.Mountain; break;
			case 'd': // ▼防衛施設建設
				char = this.php_command.Dbase; break;
			case 'm': // ▼ミサイル基地建設
				char = this.php_command.Base; break;
			case 'f': // ▼海底基地建設
				char = this.php_command.Sbase; break;
			case '-': // ▼INS 資金繰り
				char = this.php_command.DoNothing; break;
			case '.': // ▼DEL 削除
				cominput(InputPlan,3); return;
			case'\b': // ▼BS 一つ前削除
				let no = document.InputPlan.COMMAND.selectedIndex;
				if(no > 0) {
					document.InputPlan.COMMAND.selectedIndex = no - 1;
				}
				cominput(InputPlan,3);
				return;
			case '0':
			case '1':
			case '2':
			case '3':
			case '4':
			case '5':
			case '6':
			case '7':
			case '8':
			case '9':
				let m = document.InputPlan.AMOUNT.selectedIndex * 10;
				document.InputPlan.AMOUNT.selectedIndex = (m + parseInt(char,10)) % 100;
				return;
			case 'Z':
				document.InputPlan.AMOUNT.selectedIndex = 0;
				return;
			default:
				// ▼[WARN]ここに処理を入れない：IEではF5も拾えるため
				return;
		}
		this.cominput(document.InputPlan, 6, char);
		e.preventDefault();
	}
}

class Util {
	static showElement(elementId:string) {
		document.getElementById(elementId).style.display = "block";
	}
	static hideElement(elementId:string) {
		document.getElementById(elementId).style.display = "none";
	}
	static layerWrite(elementId:string, str?:string) {
		str = str || '';
		document.getElementById(elementId).innerHTML = str;
	}
	static setBGColor(elementId:string, bgColor?:string) {
		bgColor = bgColor || '#fff';
		document.getElementById(elementId).style.backgroundColor = bgColor;
	}
}

function targetOpen(part) {
	// [TODO]: ウィンドウじゃなくてモーダルに。スマホ対応は別途検討。
	let p = part.value;
	let w = window.open("{$this_file}?target=" + p, "","width={$width},height={$height},scrollbars=1,resizable=1,toolbar=1,menubar=1,location=1,directories=0,status=1");
}
