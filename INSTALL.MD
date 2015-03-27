# Pré-Requisitos

<p align=justify>Conforme mencionado anteriormente, a OfMaT funciona em ambiente Web. Assim, faz-se 
necessário instalá-la em um servidor Web com suporte a PHP. Além disso, como ela 
foi baseada no Floodlight, é necessário instalar esse controlador na rede, mas o 
Floodlight e o servidor Web podem estar em equipamentos diferentes. Caso a rede possua
virtualização, será necessário fornecer à OfMaT as informações relativas ao FlowVisor, 
hipervisor de rede usado na implementação desta primeira versão da OfMaT.</p>

<p align=justify>Para instalar a OfMaT e deixá-la em funcionamento, deve-se realizar (ou checar)
os seguintes procedimentos:
<br>
<br>1) Dispor de um espaço reservado em um servidor Web para hospedar a OfMaT;
<br>2) Obter a OfMaT através do repositório GIT (git clone https://github.com/OfMaT/OfMaT.git);
<br>3) Fazer upload da OfMaT para o servidor Web;
<br>4) Abrir o arquivo "ofmat.conf" e configurar os parâmetros adequadamente;
<br>5) Checar se o controlador Floodlight está em execução (e o FlowVisor caso haja virtualização da rede);
<br>6) Abrir um navegador e acessar a interface gráfica da OfMaT através da URL 
http://SERVIDOR_WEB/OfMaT/ ou de acordo com a orientação do administrador da rede;

<p align=justify><b>Observação 1:</b> Caso ainda não tenha o controlador Floodlight instalado, poderá obtê-lo em 
https://github.com/floodlight/floodlight (atualmente garantimos compatibilidade com a versão 0.91)
</p>

<p align=justify><b>Observação 2:</b> O FlowVisor pode ser obtido em 
git://github.com/OPENNETWORKINGLAB/flowvisor.git (OfMaT compatível com a versão 1.4)
</p>
