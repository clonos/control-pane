import replacePath from "../helpers/replacePath";

const replaceImport = (t, replacementObj, pathToReplace) => {
	const props = [];
	let firstDeclaration, firstIdentifier;
	
	for(let specifier of pathToReplace.node.specifiers) {
		if(t.isImportNamespaceSpecifier(specifier)) {
			firstDeclaration = t.variableDeclarator(firstIdentifier = specifier.local, replacementObj);
		} else {
			const isDefaultSpecifier = t.isImportDefaultSpecifier(specifier);
			
			const imported = isDefaultSpecifier ? t.Identifier("default") : specifier.imported;
			const local = specifier.local;
			
			const shorthand = !isDefaultSpecifier && imported.start === local.start && imported.end === local.end;
			const objectProp = t.objectProperty(
				imported,
				specifier.local, false, shorthand
			);
			props.push(objectProp);
		}
	}
	
	const declarations =
		firstDeclaration ?
			props.length ?
				[firstDeclaration, t.variableDeclarator(t.objectPattern(props, null), firstIdentifier)] : [firstDeclaration] :
			props.length ?
				[t.variableDeclarator(t.objectPattern(props), replacementObj)] : [];
	
	const variableDeclaration = t.variableDeclaration("const", declarations);
	
	pathToReplace.replaceWith(variableDeclaration);
};

export default function (t, path, state, calculatedOpts) {
	const pathIsImportDeclaration = path.isImportDeclaration();
	const pathToMatch = path.get("source"),
		pathToRemove = pathIsImportDeclaration && !path.node.specifiers.length && path,
		pathToReplace = pathIsImportDeclaration && path.node.specifiers.length && path;
	
	if(pathToMatch.node) {
		replacePath(t, {
			pathToMatch,
			pathToRemove,
			pathToReplace,
			replaceFn: replaceImport,
		}, calculatedOpts, state);
	}
}