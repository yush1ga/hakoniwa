type CommandRequire = 'TARGET_RIVAL' | 'POINT' | 'NET';

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
	abstract REQUIRE: CommandRequire[] | null;
	abstract exec: () => boolean;
	abstract isExecutable: () => boolean;
}


class 整地 extends CommandBase
{
	NAME = '整地';
	COST = {
		CASH: 5
	};
	REQUIRE = ['POINT'];

}
