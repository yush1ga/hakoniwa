interface CommandRequire {
	readonly TARGET_RIVAL: boolean,
	readonly POINT: boolean,
	readonly NET: boolean
}

interface CommandCost {
	readonly CASH?: number,
	readonly RATION?: number,
	readonly TIMBER?: number,
	readonly POPULATION?: number
}

interface CommandReward {
	readonly CASH?: number,
	readonly RATION?: number,
	readonly TIMBER?: number,
	readonly POPULATION?: number
}

type CommandType = 'DEVELOPMENT' | 'BUILD' | '';

abstract class CommandBase
{
	abstract NAME: string;
	abstract COST: CommandCost;
	abstract REWARD: CommandReward;
	abstract REQUIRE: CommandRequire;
    abstract exec: () => boolean;
    abstract isExecutable: () => boolean;
}


class 整地 extends CommandBase
{
	COST = {
		CASH: 5
	}
}
