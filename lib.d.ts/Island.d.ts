declare class Island
{
	name: string
	ownerName: string
	numMonster: number
	numPort: number
	ships: Ship[]
	id: number
	startTurn: number
	isBattlefield: bool
	iskeep: bool
	prizes
	absent: number
	comment: string
	commentDate: number
	password: string
	point: number
	pointPriv: number
	satelites: Satelite[]
	zins: Zin[]
	items: Item[]
	money: number
	moneyPriv: number
	numLottery: number
	food: number
	foodPriv: number
	population: number
	populationPriv: number
	area: number
	populationOf
	numDefeatMonster: number
	millitaryForceLV: number
	numLaunchableMissile: number
	weather: number
	soccer: Soccer

	income: () => void
}

type Zin = number
type Item = number
type Soccer = number[]
