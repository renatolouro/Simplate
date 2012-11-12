#Simplate 0.0.2Alfa [![Build Status](https://travis-ci.org/Chavao/Simplate.png?branch=master)](https://travis-ci.org/Chavao/Simplate?branch=master)

Esta obra foi licenciada com uma Licença Creative Commons - GNU General Public License.

Simplate Template Engine Copyright (C) 2010-2011 Silos Software e Tecnologia da Informacao LTDA ME
"This program comes with ABSOLUTELY NO WARRANTY;
This is free software, and you are welcome to redistribute it under certain conditions; see http://www.gnu.org/licenses/ for details."

"Este programa NÃO OFERECE NENHUMA GARANTIA;
Ele é um software livre e você é livre para redistribui-lo sob certas condições; acesse http://www.gnu.org/licenses/ para mais detalhes."

-----------------------------------------

## Site do Projeto
Mais detalhes em: http://www.simplate.org

## Exemplos de Uso
Veja exemplos de uso em: https://github.com/renatolouro/Simplate/wiki/Exemplos

##Simplate: um novo Template Engine para PHP
Atualmente devem haver mais de 50 sistemas de template engine para PHP publicados. Alguns mais famosos, outros praticamente desconhecidos, mas convenhamos: são muitos. E agora estamos incluindo mais um destes para a lista, pois é exatamente isso que o Simplate é: um template engine para PHP.

Mas, ao contrário do que parece, sou contra reinventar a roda. Simplate foi criado porque acredito que os que Engines que estão publicados hoje deixam de cumprir requisitos que considero como importantes para uma ferramenta do gênero. E, aparentemente, outros possuem esta mesma impressão. Dêem uma procurada em alguns fóruns e vejam o que ocorre quando alguém pergunta qual o template engine para PHP preferido. Talvez, vocês se assustem em perceber que uma parte considerável responde que prefere o próprio PHP para cuidar de seus templates, dispensando qualquer um destes sistemas.

Não parece estranho que vários programadores prefiram utilizar o próprio PHP para manipulação dos seus templates do que optar por uma das muitas ferramentas especializadas para este fim? Por que será que alguns -não poucos- tem esta opinião?

Antes de responder, devemos perguntar: Para que serve um Template Engine?
São dois motivos básicos:

* Facilitar o trabalho no layout codificado possibilitando que até pessoas não experientes em programação e, eventualmente, o próprio HTMLer ou Designer, alterem diretamente o mesmo; e
* Separar a lógica de programação e/ou código do layout.

Resta saber: Os Template Engines existentes atendem bem a estes dois requisitos? Pelo menos atendem melhor do que o próprio PHP já não poderia atender? Vamos avaliar estes itens em separado.

### Facilitando o Trabalho no Layout Codificado
Veja abaixo, um exemplo hipotético de html, como ficaria ao receber os códigos em dois template engines, além do próprio PHP e o Simplate:  

<table>
<tr><th>Tecnologia</th><th>Código</th></tr>
<tr><th width=30px;>HTML do Original do Template</th><td>&lt;select name="type_id" value=""&gt;
   &lt;option value=""&gt;Exemplo de Item&lt;/option&gt;
&lt;/select&gt;</td></tr>
<tr><th>PHP</th><td>
&lt;select name="type_id" value="&lt;?=$type_id&gt;"&gt;
&lt;?php foreach($licensee_type_list as $id=&gt;$name) { ?&gt;
   &lt;option value="&lt;?=$id?&gt;"&gt;&lt;?=$name?&gt;&lt;/option&gt;
&lt;?php } ?&gt;
&lt;/select&gt;
</td></td></tr>
<tr><th>Smarty</th><td>
&lt;select name="type_id" value="{$type_id}"&gt;
 {foreach from=$licensee_type_list key=id item=name}
   &lt;option value="{$id}"&gt;{$name}&lt;/option&gt;
 {/loop}
&lt;/select&gt;
</td></tr>
<tr><th>Dwoo</th><td>
&lt;select name="type_id" value="{$type_id}"&gt;
 {loop $licensee_type_list}
   &lt;option value="{$id}"&gt;{$name}&lt;/option&gt;
 {/loop}
&lt;/select&gt;
</td></tr>
<tr><th>Simplate</th><td>&lt;select name="type_id"  value="" #value bind=licenensee_type_list as=item scope=inner&gt;
   &lt;option value="" bind=item #value scope=inner&gt;Exemplo de Item&lt;/option&gt;
&lt;/select&gt;</td></tr>
</tr>
</table>

É verdade tanto o Smarty como o Dwoo simplificaram a marcação de início e fim do bloco PHP, alterando de &lt;?php &gt; para um simples { }. A conseqüência é que, em geral, se escreve menos. Mas, veja que são as mesmas 5 linhas, a mesma lógica, praticamente a mesma utilização. Mas, lógica do PHP foi de fato simplificada? Não parece que ambos fazem mímica do exemplo PHP? 

É exatamente por isso que alguns programadores preferem continuar usando o próprio PHP. Grande parte dos Template Engines não simplifica o template codificado quando comparado com o próprio PHP, a ponto de justificar o aprendizado de uma nova linguagem. E a verdade é que muitos dos templates engines tem se convertido em verdadeiras linguagens paralelas.  

Podemos já perceber a primeira grande vantagem do Simplate: a correspondência com o html original é direta! São as mesmas 3 linhas. E será sempre assim: correspondência linha a linha. Nenhuma tag acrescentada, nenhuma tag retirada. Sempre!

Perceba também que uma codificação Simplate pode ser aberta diretamente pelo navegador, independente de servidor Apache ou IIS. Neste caso, o HTMLer/Designer deverá ver exatamente aquilo que ele havia gerado.

Mas, e se no nosso html de exemplo o HTMLer/Designer tivesse adicionado algumas outras linhas options como exemplo? A correspondência linha a linha do Simplate continuaria? Sim! Com o - ainda não famoso - bind=fake. Esta instrução faz com que o simplate entenda que o dado referido é um mero exemplo, e deve ser retirado da compilação final. Ex.:
<table>
<tr><th>Tecnologia</th><th>Código</th></tr>
<tr><th width=30px;>HTML do Original do Template</th><td>&lt;select name="type_id" value=""&gt;
   &lt;option value=""&gt;Exemplo de Item 1 &lt;/option&gt;
   &lt;option value=""&gt;Exemplo de Item 2 &lt;/option&gt;
   &lt;option value=""&gt;Exemplo de Item 3 &lt;/option&gt;
   &lt;option value=""&gt;Exemplo de Item 4 &lt;/option&gt;
&lt;/select&gt;</td></tr>
<tr><th>Simplate</th><td>&lt;select name="type_id"  value="" #value bind=licenensee_type_list as=item scope=inner&gt;
&lt;option value="" bind=item #value scope=inner&gt;Exemplo de Item 1&lt;/option&gt;
&lt;option value="" bind=fake&gt;Exemplo de Item 2&lt;/option&gt;
&lt;option value="" bind=fake&gt;Exemplo de Item 3&lt;/option&gt;
&lt;option value="" bind=fake&gt;Exemplo de Item 4&lt;/option&gt;
&lt;/select&gt;
</tr>
</table>

Isto cria muitas possibilidades. Por exemplo, um programador pode enviar por email o template codificado e perguntar ao HTMLer/Designer: O que ficou errado na montagem? 

Mas, já que foi falado sobre o Smarty e o Dwoo, uma análise séria não poderia estar completa se não comentássemos sobre o TinyButStrong. Ele está numa categoria diferente dos dois chamada de 'natural templates'. O Simplate também estaria nesta categoria. Os 'natural tamplates' são todos os template engines que não alteram a estrutura do html, assim como o Simplate. Mas, o Simplate vai ainda além, procurando respeitar também o conteúdo original do template, com todos os seus exemplos de utilização. O TinyButStrong- que merece muito ser conhecido-, coloca seus comandos exatamente no lugar do que seria o conteúdo. Sem falar que, na minha opinião, seus comandos são menos intuitivos. Simplate se torna assim ainda menos intrusivo!  

Ser o menos intrusivo possível no Layout/HTML causa duas conseqüências diretas:
<ul>
<li>
Diminui consideravelmente os erros de montagem do HTML+(Tags PHP ou de outro Engine); e 
</li>   
<li>Caso, ainda assim, o erro surja -digamos que o fechamento de uma tag seja excluído por acidente- o HTMLer ou Designer encontrará um código muito mais limpo e familiar para ajudar na depuração, pois verá o seu próprio código com apenas alguns atributos a mais em algumas tags.</li>
</ul>

Fecharemos este tópico com a 1a Premissa do Desenvolvimento Simplate:
- O HTML original deve ser preservado. O Simplate deve permitir que o layout já codificado abra diretamente no navegador apresentando exatamente a mesma forma e conteúdo de quando foi criado.

### Separando o Layout da Lógica
Isto alguns Template Engines fazem até bem. Mas, outros nem tanto. Como falado, muitos deles têm se convertido numa linguagem à parte, e até poderosa, com direito a IFs, Cases, For, manipulação de variáveis etc.
Em resumo, a lógica pode tranqüilamente aparecer no layout. O que salva isso é o cuidado do programador. E se é para depender do cuidado do programador para evitar o aparecimento da lógica no layout, segue a pergunta recorrente: Por que não utilizar o próprio PHP? 

O Simplate possui um número reduzido de comandos que realmente evita o aparecimento de lógica de programação no template codificado. Por exemplo, não temos IF e não sentimos falta dele. Caso seja necessário saber se um bloco deve ou não aparecer, podemos utilizar o próprio comando bind. Um bind em qualquer valor nulo ou indefinido fará com que o bloco do mesmo não apareça.

Na verdade o bind serve para muita coisa:
<ul>
<li>Não temos include, pois um bind em um objeto Simplate inclui outro template;</li>
<li>Não temos IF, pois um bind em um valor nulo ou não pode fazer um bloco sumir ou não; e</li>
<li>Não temos For, pois um bind em um Iterador, provoca a repetição.</li>
</ul>

Simples isso, não? :)

E segue agora a 2a Premissa do Desenvolvimento Simplate:
- Mantenha o Simplate simples!

<h2>Dois Requisitos e Duas Premissas que nos levaram ao Simplate</h2>
Resumindo, esta é a pequena história do Simplate. Encontramos com os dois requisitos que acreditamos que não estavam sendo cumpridos de forma apropriada:

<ul>
<li>Facilitar o trabalho no layout codificado possibilitando que até pessoas não experiêntes em programação e, eventualmente, o próprio HTMLer ou Designer, alterem diretamente o mesmo; e</li>
<li>Separar a lógica de programação e/ou código do layout.</li>
</ul>

Para atender estes requisitos melhor do que qualquer outro Template Engine, foram criadas as duas premissas. Cada uma indo ao encontro de um requisito:

<ul>
<li>Os comandos Simplate devem preservar o HTML original, mantendo conteúdo e a forma do mesmo; e</li>
<li>Mantenha o Simplate simples!</li>
</ul>

Estes são os dois princípios que norteiam o desenvolvimento do Simplate. Qualquer decisão de projeto que tenha que ser tomada é comparada com estes dois princípios.

E você? Gostou destes princípios? É o que você busca? Então? Que tal se juntar ao time? Ainda existe muito por fazer.

E seja bem vindo ao SIMPLATE! 
Esperamos que Goste!
<HR />
<strong>Renato Louro (@rslouro)</strong>
