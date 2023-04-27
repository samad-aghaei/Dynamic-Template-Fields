
if( process.argv.length <= 3 )
{
	var object = {}

	var lastKey = undefined
	var arg = undefined
	var isMap = false

	const handlers = {
		get ( target, propKey, receiver )
		{
			const prop = target[propKey];

			if ( prop != null )
			{
				return prop
			}

			if( typeof propKey !== 'symbol' )
			{
				if( propKey !== 'map' )
				{
					if( target.isInFunction )
					{
						if( arg )
						{
							object[ lastKey ] = {[propKey]: ''}
						}
					}
					else
					{
						object[propKey] = ''
						arg = undefined
						isMap = false
					}	

					if(isMap)
					{
						object[ lastKey ][propKey] = ''
					}
					else
					{
						lastKey = propKey
					}
				}
			}


		return new Proxy( ()=>{},
		{
			get( target, propKey, receiver )
			{
				if( propKey === 'map' )
				{
					isMap = true
				}

				return handlers.get( target, propKey, receiver )
			},

			apply( target, propKey, receiver )
			{

		    arg = receiver[0].toString().match(/([a-zA-Z]\w*|\([a-zA-Z]\w*(\s*[a-zA-Z]\w*)*\))\s*(,|=>)/)?.[1].trim()

			    if( arg )
			    {
			    	object[ lastKey ] = []
			    }

			    if( typeof receiver[0] === 'function' )
			    {

			    	return Reflect.apply( receiver[0], undefined, [new Proxy( {isInFunction: true }, handlers )] )
			    }

			    return
			}
		});
		}
	};

	const items = new Proxy( {}, handlers )
	const settings = new Proxy( {}, handlers )

	eval(`\`${ require('fs').readFileSync( process.argv[2], "utf-8") }\``)
	console.log(JSON.stringify(object))
}
else
{
	Array.prototype.map = ( arrayMap =>
		function map( callback )
		{
			return Reflect.apply( arrayMap, this, [ callback ] ).join( '' )
		}
	)( Array.prototype.map );

	var items = JSON.parse(process.argv[3])
	var settings = JSON.parse(process.argv[4])

	//const ajax = '<script src="./ajax.js"></script>'

	console.log(eval(`\`${ require('fs').readFileSync( process.argv[2], "utf-8") }\``))
}







