/**
 * Cria uma instância do framework para a utilização de funções comuns na camada de view.
 * O objetivo desta função é tornar não necessário a utilização das respectivias função em PHP
 * e permitir o acesso de variáveis de backend enviadas através de $this->addVarView() no controller. 
 * Exemplo:
 * - getUrlController(), getNameController, etc.
 * - getVar('<nome da variável>') => udev.getVar('usuarios').
 * 
 * @param {Array} segments Segmentos da url 
 * @param {Object} vars Variáveis em formato JSON
 */
function iniUdev(segments, vars)
{
    return {
        "vars" : vars,
        getVar : function (variable)
        {
            return this.vars[variable];
        },
        "url" : {
            "segments" : segments,
            getNameModule : function ()
            {
                return this.segments[0];
            },
            getNameController : function ()
            {
                return this.segments[1];
            },
            getNameAction : function ()
            {
                return this.segments[2];
            },
            getUrlModule : function (abs)
            {
                return this.createUrlFromSegment(1, abs); 
            },
            getUrlController : function (abs)
            {
                return this.createUrlFromSegment(2, abs); 
            },
            getUrlAction : function (abs)
            {
                return this.createUrlFromSegment(3, abs); 
            },
            getSegments : function (segment)
            {
                return this.segments;
            },
            getSegment : function (offset = 3)
            {
                return this.segments[offset-1];
            },
            createUrlFromSegment : function(segment, abs)
            {
                var segments = this.segments.slice(0, segment),
                    url = '/'+segments.join('/');
                return abs ? window.location.protocol+'//'+window.location.hostname+url : url;
            },
            getFullUrl : function ()
            {
                return window.location.href;
            },
        }
    }
}