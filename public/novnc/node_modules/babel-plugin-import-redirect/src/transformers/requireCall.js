import replacePath from "../helpers/replacePath";

const membToString = (t, {object, property, computed}) => {
	const propIsIdentifier = t.isIdentifier(property);
	if(!object.name || (computed && propIsIdentifier)) return null;
	return object.name + "." + (propIsIdentifier ? property.name : property.value);
};

const replaceRequire = (t, replacementObj, pathToReplace, wrapReplacementInPromise) => {
	const {node, node: {callee}} = pathToReplace;
	
	if(t.isImport(callee) ||
	(t.isIdentifier(callee) && wrapReplacementInPromise.has(callee.name) )||
	// don't check further if there nothing in wrapReplacementInPromise
	(wrapReplacementInPromise.size > 0 && t.isMemberExpression(callee) && wrapReplacementInPromise.has(membToString(t, callee)))) {
		const promise = t.memberExpression(t.identifier("Promise"),t.identifier("resolve"));
		replacementObj = t.callExpression(promise, [replacementObj]);
	} else if(pathToReplace.parentPath.isMemberExpression({object: node})) {
		replacementObj = t.parenthesizedExpression(replacementObj);
	}
	
	pathToReplace.replaceWith(replacementObj);
};

export default function (t, path, state, calculatedOpts) {
	const {callee} = path.node, {functionNames} = calculatedOpts;
	if((t.isIdentifier(callee) && functionNames.has(callee.name)) ||
		t.isImport(callee) ||
		// don't check further if there is only "require" in functionNames
		(functionNames.size > 1 && t.isMemberExpression(callee) && functionNames.has(membToString(t, callee)))
	) {
		
		const pathToMatch = path.get("arguments.0"), pathToRemove = path.parentPath.isExpressionStatement() && path.parentPath;
		if(pathToMatch.isStringLiteral()) {
			replacePath(t, {
				pathToMatch,
				pathToRemove,
				pathToReplace: !pathToRemove && path,
				replaceFn: replaceRequire,
			}, calculatedOpts, state);
		}
	}
}