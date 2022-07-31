import resolveNode from "./resolveNode";
import match from "./matchRedirect";
import {relative, extname} from "path";

export default function (t, {pathToMatch, pathToRemove, pathToReplace, replaceFn}, {toMatch, toRemove, toReplace, basedir, wrapReplacementInPromise}, {opts: {root, extensions, suppressResolveWarning}}) {
	const requiredFilename = resolveNode(basedir, pathToMatch.node.value, extensions, suppressResolveWarning);
	const matched = match(requiredFilename, toMatch, root, extensions, suppressResolveWarning);

	if(matched !== null) {
		const {redirect, redirected} = matched;

		// path has a corresponing redirect
		if(redirected !== null) {
			if(redirected.includes("/node_modules/")) {
				const resolvedFromFile = resolveNode(basedir, redirect, extensions, suppressResolveWarning);

				// require(redirect) resolves to the same path from file source as require(redirected)
				if(resolvedFromFile === redirected) {
					pathToMatch.replaceWith(t.stringLiteral(redirect));
					return;
				} else {
					const modulePath = redirected.match("^.*/node_modules/([^/]+?)(?=/)");
					if(modulePath) {
						const [moduleDir, moduleName] = modulePath;
						// require(modulePath) resolves to the same file as require(modulePath/file/path)
						// thanks to package.json
						if(resolveNode(moduleDir, moduleName, extensions, suppressResolveWarning) === redirected) {
							pathToMatch.replaceWith(t.stringLiteral(relative(basedir, moduleDir)));
							return;
						}
					}
				}
			}

			let relativeRedirect = relative(basedir, redirected);
			if(!(relativeRedirect.startsWith("./") || relativeRedirect.startsWith("../"))) relativeRedirect = "./" + relativeRedirect;

			if(!extname(redirect)) {
				const ext = extname(relativeRedirect);
				if(ext) relativeRedirect = relativeRedirect.slice(0, -ext.length);
			}
			pathToMatch.replaceWith(t.stringLiteral(relativeRedirect));
		}
	// if can be removed
	} else if(pathToRemove) {
		if(toRemove.some(regexp => regexp.test(requiredFilename)) || toReplace.find(([regexp]) => regexp.test(requiredFilename))) {
			pathToRemove.remove();
		}

	// if can be replaced
	} else if(pathToReplace) {
		const replacement = toReplace.find(([regexp]) => regexp.test(requiredFilename));

		if(replacement) {
			replaceFn(t, replacement[1], pathToReplace, wrapReplacementInPromise);
		}
	}
}
