import resolveNode from "./resolveNode";

export default function (filename, regexps, root, extensions, suppressResolveWarning) {
	const redirectPair = regexps.find(([regexp]) => regexp.test(filename));
	
	if(redirectPair) {
		let [regexp, redirect] = redirectPair;

		// if redirect is of "different/path/$1.js" form
		if(/\$\d/.test(redirect)) {
			// "abs/path/to/path/lib.js".match(/path/(\w+).js$/)[0] -> "path/lib.js"
			// "path/lib.js".replace(/path/(\w+).js$/, "different/path/$1.js") -> "different/path/lib.js"
			// redirect = "different/path/lib.js"
			redirect = filename.match(regexp)[0].replace(regexp, redirect);
		}
		
		return {
			redirected: resolveNode(root, redirect, extensions, suppressResolveWarning),
			redirect
		};
	}
	
	return null;
}