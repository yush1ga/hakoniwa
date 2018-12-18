type BasisType = 'land' | 'sea' | 'shallows' | 'mountain'

abstract class StructureBase
{
	abstract cost: number
	abstract additionalCost?: []
	abstract basisType: BasisType
	abstract isFilled: () => boolean
}

class 農地 extends StructureBase
{

}
