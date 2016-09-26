	function translate(phrase)
	{
    	    if(typeof this.lang!='undefined')
    	    {
        	if(typeof this.lang[phrase]!='undefined')
            	    return this.lang[phrase];
    	    }
    	    return phrase;
	}
