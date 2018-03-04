class Hakoniwa {

	constructor(_props:any) {
		// code...
	}
}

class IslandView extends React.Component<any, any> {

	constructor(props:any) {
		super(props)
	}

}
class MapCell extends React.Component<any, any> {

	constructor(props:any) {
		super(props)
	}
	render() {
		return (
			<td
				data-inverted=""
				data-html={this.getTooltip()}
				onMouseOver={this.onMouseover}
			>
				<img
					src={this.getImgSrc(this.props.landId, this.props.meta)}
					data-x={this.props.posX}
					data-y={this.props.posY}
					data-landId={this.props.landId}
					data-meta={this.props.meta}
				/>
			</td>
		)
	}
	getImgSrc = (id:number, meta:number) => {
		console.log(id, meta)
		return ``
	}
	getTooltip = () => {
		return ``
	}
	onMouseover = () => {
		console.log(this)
	}
}
