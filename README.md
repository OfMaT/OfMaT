# OfMaT - OpenFlow Management Tool

A OfMaT é uma ferramenta para a gerência de comutadores OpenFlow, que consiste
em um software que deve ser instalado em um computador que tenha comunicação lógica
com a rede que será gerenciada. Seu projeto contemplou a possibilidade de ser usada tanto
em redes com virtualização quanto em redes sem virtualização. Além disso, a OfMaT
pode ser usada na maioria das redes OpenFlow, pois não interfere com o controlador já
existente nessa rede.

# Implementação

Para o desenvolvimento da OfMaT foi utilizada a linguagem de programação PHP
e HTML com CSS. A ferramenta é compatível com a especificação 1.0 do OpenFlow, e no momento
está sendo desenvolvida sua atualização para também atender a especificação 1.3 do OpenFlow.

# Interface Gráfica

OfmAt possui uma interface gráfica, sendo acessível através de um navegador web.
Seu menu superior fornece acesso a todas as funções existentes e sua documentação
também pode ser acessada através desse menu. Suas principais funcionalidades estão
disponíveis através das opcões Switch View e Slice View. Caso a ferramenta seja
instalada em uma rede que não usa virtualização, o menu Slice View não terá nenhuma
funcionalidade.

# Pré-Requisitos

Conforme mencionado anteriormente, a OfMaT funciona em ambiente Web. Assim, faz-se 
necessário instalá-la em um servidor Web com suporte a PHP. Além disso, como ela 
foi baseada no Floodlight, é necessário instalar esse controlador na rede, mas o 
Floodlight e o servidor Web podem estar em equipamentos diferentes. Caso a rede possua
virtualização, será necessário fornecer à OfMaT as informações relativas ao FlowVisor, 
hipervisor de rede usado na implementação desta primeira versão da OfMaT.

A OfMaT contém um arquivo de configuração chamado “ofmat.conf”, onde se definem algumas
opções da ferramenta. Dentre elas, os endereços de rede do Floodlight e FlowVisor 
(se houver virtualização), e as credenciais de acesso.

Para colocar a OfMaT em funcionamento, deve-se inicializar o servidor Web e então rodar 
o Floodlight. A partir daí, já é possível acessar a OfMaT através de um navegador Web.

# Contato
Se tiver alguma dúvida sobre a OfMaT, pode nos contatar através do e-mail 
ruialexandrefigueira@gmail.com. Esta ferramenta ainda está em desenvolvimento. 
Sugestões, comentários e relatos de bugs são bem-vindos.
