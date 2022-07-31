import requireCall from './transformers/requireCall';
import importExportDeclaration from './transformers/importExportDeclaration';
import {parseExpression} from "babylon";
import {dirname} from "path";

const defaultExtensions = [".js", ".jsx", ".es", "es6"];

export default ({types: t}) => {
	return {
		pre(state) {
			const opts = this.opts;
			if(!opts.root) opts.root = process.cwd();

			const filenameUnknown = state.opts.filename === "unknown";
			const basedir = filenameUnknown ? opts.root : dirname(state.opts.filename);
			if(filenameUnknown && !opts.suppressResolveWarning) {
				console.warn("Source input isn't a file. Paths will be resolved relative to", opts.root);
			}
			
			if(!opts.extensions) opts.extensions = defaultExtensions;
			
			const toMatch = [], toRemove = [], toReplace = [], {redirect} = opts;
			for(let pattern of Object.keys(redirect)) {
				const regexp = new RegExp(pattern), redirected = redirect[pattern];
				
				if(redirected === false) {
					toRemove.push(regexp);
				}else if(typeof redirected === "string") {
					toMatch.push([regexp, redirected]);
				} else {
					toReplace.push([regexp, parseExpression(JSON.stringify(redirected))]);
				}
			}
			
			const {extraFunctions, promisifyReplacementFor} = this.opts;
						
			const functionNames = new Set(extraFunctions && (Array.isArray(extraFunctions) ? extraFunctions : [extraFunctions])).add("require");
			const wrapReplacementInPromise = new Set(promisifyReplacementFor && (Array.isArray(promisifyReplacementFor) ? promisifyReplacementFor: [promisifyReplacementFor]));
			
			this.calculatedOpts = {
				basedir,
				toMatch,
				functionNames,
				wrapReplacementInPromise,
				toRemove,
				toReplace
			};
		},
		visitor: {
			CallExpression(path, state) {
				requireCall(t, path, state, this.calculatedOpts);
			},
			ModuleDeclaration(path, state) {
				importExportDeclaration(t, path, state, this.calculatedOpts);
			}
		}
	};
};