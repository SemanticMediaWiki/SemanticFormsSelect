/*
 * return a jquery pattern string to find out form fields which values will be changed
 * 
 */
function SFSelect_getSelectFieldPat(nameObj, f )
{
	var selectpat = "";
	if (f.selectismultiple)
	{
		if (f.selecttemplate == f.valuetemplate)
		{
			// each select field in a multiple
			// template depends on its value field.

			var pat="select[name='"+f.selecttemplate+"["+nameObj.index+"]["+f.selectfield+"]']";
			var pat1="select[name='"+f.selecttemplate+"["+nameObj.index+"]["+f.selectfield+"][]']";
			selectpat=pat+","+pat1;
		} else
		{
			// multiple select fields depends on one
			// value field.
			selectpat = "select[name^='"
					+ f.selecttemplate
					+ "'][name$='["
					+ f.selectfield
					+ "]'], select[name^='"
					+ f.selecttemplate
					+ "'][name$='["
					+ f.selectfield + "][]']";
		}

	} else
	{
		selectpat = "select[name='"
				+ f.selecttemplate + "["
				+ f.selectfield
				+ "]'], select[name='"
				+ f.selecttemplate + "["
				+ f.selectfield + "][]']";
	}
	return selectpat;
}


/*
 * SF form add a fobj for each field in a multiple template.
 * In reality we only need a fobj to reduce the ajax call.
 */
function SFSelect_removeDuplicateFobjs( SFSelect_fobjs )
{
	var newfobjs = [];
	for(var i=0; i<SFSelect_fobjs.length; i++)
	{
		var found=false;
		var of=SFSelect_fobjs[i];
		if (!of.selectismultiple)
		{
			newfobjs.push(of);
			continue;
		}
		for (var j=0; j<newfobjs.length; j++)
		{
			var nf=newfobjs[j];
			if (of.selecttemplate==nf.selecttemplate && of.selectfield==nf.selectfield)
			{
				found=true;
				break;
			}
		}
		if (!found)
		{
			newfobjs.push(of);
		}
	}

	return newfobjs;
	
}


/*
 * Parse the SF field name into an objetc for easy process
 */
function SFSelect_parseName(name)
{
	var names=name.split('[');
	var nameObj={template:names[0]};
	if(names[names.length-1]==']')
	{
		nameObj.isList=true;
		var property=names[names.length-2]
		property=property.substr(0, property.length-1);
		nameObj.property=property;
		if (names.length==4)
		{
			var index=names[1];
			index=index.substr(0, index.length-1);
			nameObj.index=index;
		} else
		{
			nameObj.index=null;
		}
	} else
	{
		nameObj.isList=false;
		var property=names[names.length-1]
		property=property.substr(0, property.length-1);
		nameObj.property=property;
		if (names.length==3)
		{
			var index=names[1];
			index=index.substr(0, index.length-1);
			nameObj.index=index;
		} else
		{
			nameObj.index=null;
		}
	}
	return nameObj;
}

function SFSelect_setDependentValues (nameobj, fobj, values){

	var selectPat=SFSelect_getSelectFieldPat(nameobj, fobj);
	jQuery(selectPat).each(function(index, element){
		//keep selected values;
		var selectedValues=jQuery(element).val();
		if (!selectedValues){
			selectedValues=[];
		} else if (!jQuery.isArray(selectedValues)){
			selectedValues=[selectedValues];
		}

		element.options.length=values.length;

		var newselected=[];

		if ( fobj.label ) {
			var namevalues = SFSelect_processNameValues( values );

			for(var i=0; i<namevalues.length; i++){
				element.options[i]=new Option(namevalues[i][1], namevalues[i][0]);
				if (jQuery.inArray(namevalues[i][0], selectedValues)!=-1){
					element.options[i].selected=true;
					newselected.push(namevalues[i][0]);
				}
			}
		} else {

			for(var i=0; i<values.length; i++){
				element.options[i]=new Option(values[i]);
				if (jQuery.inArray(values[i], selectedValues)!=-1){
					element.options[i].selected=true;
					newselected.push(values[i]);
				}
			}
		}

		if (newselected.length==0){
			if (fobj.selectrm && fobj.selecttemplate != fobj.valuetemplate&&fobj.selectismultiple){
				jQuery(element).closest("div.multipleTemplateInstance").remove();
			} else{
				if (selectedValues.length!=0)
					jQuery(element).trigger("change");
			}
		} else if (!SFSelect_arrayEqual(newselected, selectedValues)){
			jQuery(element).trigger("change");
		}
	});
}

/** Function for turning name values from 'Page (property)' results **/

function SFSelect_processNameValues( values ) {

	var namevalues = [];

	var regex = " (";

	for(var i=0; i<values.length; i++){

		var postval = values[i].split( regex );
		if ( postval.length < 2 ) {
			namevalues[i] = [ values[i], values[i] ];
		} else if ( postval.length === 2 ) {
			var label = postval[1].replace(/\)\s*$/, "");
			var value = postval[0];

			namevalues[i] = [ value, label ];
		} else {
			var last = postval.length - 1;
			var label = postval[last].replace(/\)\s*$/, "");

			var slice = postval.slice(0, last-1);
			var value = slice.join( " (" );

			namevalues[i] = [ value, label ];
		}
	}

	return namevalues;

}

function SFSelect_arrayEqual(a, b)
{
	if (a.length!=b.length)
		return false;
	a=a.sort();
	b=b.sort();
	for (var i=0; i<a.length; i++)
	{
		if (a[i]!=b[i])
			return false;
	}
	return true;
}
/*
 * fobj has the following fields
 * 
 * valuetemplate:string, 
 * 
 * valuefield:string, value is the form field on which other select element depends on. change on this field will trigger a load event for selectfield. 
 * 
 * selecttemplate:string 
 * 
 * selectfield:string
 * 
 * selectismultiple:boolean, Whether this template is a multiple template.
 * 
 * selectquery or selectfunciton: the query ot function to execute
 * 
 * selectrm:boolean remove the div if the selected value for a field is not valid any more.
 *
 * label: boolean, process ending content () as label in option values.
 *
 * sep: Separator for the list of retrieved values, default ','
 */
function SFSelect_changeHandler (src)
{
	
	//console.log("change is called with "+src.name);
	if (src.tagName.toLowerCase()!='select'&& src.tagName.toLowerCase()!='input')
	{
		return;
	}
	var v=jQuery(src).val();
	if (jQuery.isArray(v))
	{

	} else if (v==null)
	{
		v=[];
	}  else
	{
		v=[v];
	}
	var srcName=SFSelect_parseName(src.name);
	for(var i=0; i<SFSelect_fobjs.length; i++)
	{
		var fobj=SFSelect_fobjs[i];
		if (srcName.template==fobj.valuetemplate && srcName.property==fobj.valuefield)
		{
			//good, we have a match.
			// No values
			if (v.length==0||v[0]==''){ 
				SFSelect_setDependentValues(srcName, fobj, []);
			} else {
				// Values
				var param = {}
				param['action'] = 'sformsselect';
				param['format'] = 'json';
				param['sep'] = fobj.sep;

				if (fobj.selectquery){
					var query = fobj.selectquery.replace("@@@@", v.join('||'));
					param['query'] = query;
					param['approach'] = 'smw';

				} else {
					var query = fobj.selectfunction.replace("@@@@", v.join(","));
					param['query'] = query;
					param['approach'] = 'function';
				}

				var posting = jQuery.get( mw.config.get('wgScriptPath')  + "/api.php", param );
				posting.done(function( data ) {
					// Let's pass values
					SFSelect_setDependentValues(srcName, fobj, data["sformsselect"].values);
				}).fail( function( data ) { console.log("Error!");});
				
				break; // Avoid loading fobj again

			}

		}

	}
}


/*
 * We expect 
 * SFSelect_fobjs is an array
 */
jQuery(function()
{
	//simplify duplicated object.
	SFSelect_fobjs = SFSelect_removeDuplicateFobjs( SFSelect_fobjs );
	jQuery("form#sfForm").change(function(event){
		SFSelect_changeHandler(event.target);
	});
	
	
	var objs=null;
	//fields loading at load time.
	for (var i=0; i<SFSelect_fobjs.length; i++){
		
		var fobj= SFSelect_fobjs[i];
		var valuepat= "input[name=" + fobj.valuetemplate + "\\["+ fobj.valuefield + "\\]]";

		if ($(valuepat).val()){
			objs=jQuery(valuepat);
		} else{
			valuepat= "select[name=" + fobj.valuetemplate + "\\["+ fobj.valuefield + "\\]]";
			objs=jQuery(valuepat);
		}
		
		objs.trigger("change");
	
	}
	
});

