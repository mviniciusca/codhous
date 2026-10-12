# MEGA PROMPT — AI POST STUDIO
## Plataforma profissional de criação gráfica com inteligência artificial

Assuma o papel de um **Staff Software Engineer, Senior Product Designer, especialista em ferramentas gráficas, UX de SaaS, edição visual no navegador e integração com modelos generativos de IA**.

Sua missão é transformar o projeto atual em uma plataforma profissional de criação de artes publicitárias, posts para redes sociais e materiais de marketing, com uma experiência inspirada na facilidade de uso do Canva, na flexibilidade de um editor gráfico moderno e na capacidade criativa dos modelos generativos de imagens.

Não quero apenas uma tela bonita, um protótipo estático ou um CRUD convencional. Quero uma aplicação real, funcional, extensível, visualmente refinada e preparada para evoluir para um produto comercial.

O usuário final deve conseguir fornecer a identidade visual de sua empresa, enviar imagens de referência, descrever uma campanha, gerar uma arte com IA, personalizar completamente o resultado, criar variações de cores e baixar os arquivos prontos para publicação.

---

# 1. PRIMEIRA ETAPA: ANALISE O PROJETO EXISTENTE

Antes de implementar qualquer coisa:

1. Inspecione completamente o repositório atual.
2. Identifique a stack, as versões das dependências, a arquitetura, as convenções e os padrões existentes.
3. Analise as páginas, componentes, rotas, layouts, serviços, modelos, migrations, testes e configurações relevantes.
4. Identifique quais funcionalidades já existem e podem ser reutilizadas.
5. Identifique os pontos que precisam de refatoração e as dependências que estão faltando.
6. Verifique o sistema de autenticação, armazenamento de arquivos, processamento em background e integrações existentes.
7. Determine a estratégia de implementação que melhor se encaixa no projeto real.

**Regras obrigatórias:**

- Não recrie a aplicação do zero se já existir uma estrutura funcional.
- Não substitua tecnologias existentes sem uma justificativa técnica concreta.
- Não introduza dependências desnecessárias.
- Não sobrescreva funcionalidades existentes sem compreender seu propósito.
- Preserve a compatibilidade com a stack e as versões instaladas.
- Siga os padrões de nomenclatura, organização e qualidade já adotados pelo projeto.
- Não invente serviços externos, APIs, credenciais ou recursos que não estejam disponíveis.
- Se uma funcionalidade depender de credenciais externas, implemente uma integração preparada para configuração e explique o que falta.
- Não pare depois da análise: apresente um plano curto e comece a implementação.

Se o projeto utilizar Laravel, priorize uma arquitetura compatível com Laravel, Eloquent, validação nativa, policies, filas, storage e os componentes frontend já instalados. Se utilizar Filament, Livewire, Vue ou React, respeite a arquitetura encontrada e integre a solução sem criar uma segunda aplicação desnecessária.

---

# 2. VISÃO DO PRODUTO

Crie um produto chamado provisoriamente **AI Post Studio**, permitindo que qualquer pessoa crie materiais gráficos profissionais sem precisar dominar ferramentas de design.

O fluxo principal deve ser:

1. O usuário entra no estúdio.
2. Cria um novo projeto ou escolhe um template.
3. Envia a logo e as imagens da empresa.
4. Descreve o que deseja produzir.
5. Escolhe um estilo visual, uma paleta de cores e o formato de publicação.
6. A IA gera uma composição inicial.
7. O usuário edita textos, imagens, cores, formas e demais elementos.
8. Gera variações da mesma campanha.
9. Compara os resultados.
10. Exporta a arte em alta qualidade.
11. Salva o projeto para continuar editando posteriormente.

O sistema precisa separar três conceitos:

- **Projeto:** documento editável que reúne todos os elementos e configurações.
- **Arte:** composição visual produzida dentro do projeto.
- **Exportação:** arquivo final renderizado para publicação.

Uma arte exportada não deve substituir o documento editável original.

O usuário precisa conseguir voltar ao projeto e modificar sua composição a qualquer momento.

---

# 3. EXPERIÊNCIA VISUAL E DIREÇÃO DE DESIGN

Crie uma interface de SaaS premium, contemporânea, intuitiva e visualmente consistente.

A experiência deve transmitir a sensação de um verdadeiro estúdio criativo.

## Direção visual

- Interface clara, com superfícies brancas ou cinza muito suave.
- Tipografia moderna, legível e com hierarquia forte.
- Azul vibrante para ações primárias e estados selecionados.
- Laranja como cor de destaque para ações criativas e elementos da marca.
- Bordas discretas, sombras sutis e cantos arredondados.
- Ícones consistentes e facilmente reconhecíveis.
- Espaçamento generoso sem desperdício de área útil.
- Feedback visual para seleção, carregamento, sucesso e erro.
- Transições rápidas e discretas.
- Estados vazios bem projetados.
- Menus, painéis e controles com acabamento profissional.

Não transforme toda a interface em uma coleção de cards. Use cards onde fizerem sentido e priorize uma estrutura de workspace com painéis funcionais.

Evite gradientes excessivos, glassmorphism indiscriminado, sombras exageradas e elementos decorativos sem função.

O editor deve aproveitar a área disponível da tela.

## Layout principal

Desenvolva uma estrutura semelhante a esta:

### A. Barra superior

Deve conter:

- Logotipo e nome do produto.
- Nome editável do projeto.
- Indicador de salvamento.
- Desfazer.
- Refazer.
- Controle de zoom.
- Ajuste de visualização.
- Salvar.
- Baixar.
- Menu com ações adicionais.

O usuário precisa saber se suas alterações estão salvas.

### B. Barra lateral de ferramentas

Disponibilize as seguintes áreas:

- Criar com IA.
- Templates.
- Uploads.
- Texto.
- Imagens.
- Elementos.
- Formas.
- Ícones.
- Fundos.
- Cores.
- Camadas.
- Histórico.

A barra lateral deve permitir alternar entre ferramentas sem perder o estado atual do documento.

### C. Painel contextual esquerdo

Seu conteúdo deve mudar de acordo com a ferramenta selecionada.

Exemplos:

- Templates: busca, categorias, filtros e miniaturas.
- Uploads: imagens enviadas, busca e organização.
- Texto: estilos tipográficos, tamanhos e presets.
- Elementos: formas, ícones e elementos gráficos.
- Camadas: hierarquia, visibilidade, bloqueio e ordenação.
- Cores: paletas, cores recentes e personalização.

### D. Área central de trabalho

A área central deve funcionar como um canvas gráfico.

Requisitos:

- Fundo neutro para destacar a arte.
- Canvas centralizado quando couber.
- Zoom ajustável.
- Navegação pela área de trabalho.
- Seleção de elementos.
- Controles de transformação.
- Guias de alinhamento.
- Indicadores de posição e dimensões.
- Opção de visualizar a arte sem as ferramentas.
- Redimensionamento visual consistente.
- Suporte a diferentes proporções de publicação.

A arte não pode ser apenas uma imagem decorativa sem possibilidade de edição.

### E. Painel contextual direito

Deve apresentar as propriedades do elemento selecionado ou as opções da ferramenta ativa.

Exemplos:

- Cor de preenchimento.
- Cor do texto.
- Tipografia.
- Tamanho.
- Alinhamento.
- Opacidade.
- Bordas.
- Espaçamento.
- Posição.
- Dimensões.
- Imagem de fundo.
- Ajustes de imagem.
- Configurações de exportação.

Não mostre todas as propriedades ao mesmo tempo. Utilize painéis contextuais organizados.

---

# 4. SISTEMA DE CRIAÇÃO COM IA

A IA é um dos principais diferenciais do produto.

Crie uma experiência de criação por briefing, em que o usuário descreve a arte que deseja e fornece materiais de referência.

## Entrada do briefing

Disponibilize:

- Campo de texto amplo.
- Upload de imagens de referência.
- Upload da logo.
- Seleção de finalidade.
- Seleção de formato.
- Estilo visual.
- Paleta de cores.
- Tom da comunicação.
- Preferência por fotografia, ilustração ou composição mista.
- Opção de usar a identidade visual da empresa.

Exemplo de briefing:

"Crie um post profissional para uma empresa de concreto usinado anunciando serviços de bombeamento. Utilize uma fotografia realista de uma obra, destaque o benefício de agilidade, mantenha a identidade visual da empresa e inclua um CTA para solicitar orçamento."

O usuário não deve ser obrigado a preencher dezenas de campos. O briefing deve aceitar linguagem natural e apresentar opções rápidas para quem preferir.

## Processo de geração

O fluxo ideal é:

1. Interpretar o briefing.
2. Identificar o objetivo comercial.
3. Identificar o público e o tom desejado, quando informados.
4. Definir uma hierarquia visual.
5. Escolher uma composição apropriada.
6. Determinar a paleta de cores.
7. Preparar os elementos gráficos.
8. Gerar imagens quando necessário.
9. Montar a arte editável.
10. Apresentar o resultado no canvas.
11. Permitir ajustes e novas versões.

A geração deve ter estados claros: aguardando, processando, concluída e falhou.

Se o provedor suportar processamento assíncrono, utilize o mecanismo apropriado para acompanhar o resultado.

Não bloqueie desnecessariamente a interface durante uma geração demorada.

## Arquitetura de integração

Crie uma camada de abstração para provedores de IA.

Separe:

- Interpretação de briefing.
- Geração de imagens.
- Remoção de fundo.
- Tratamento de imagens.
- Geração de textos.
- Geração de variações.
- Processamento e composição da arte.

Não misture regras de negócio diretamente com chamadas HTTP a um provedor específico.

Se o projeto não possuir uma API de IA configurada:

- Implemente a arquitetura da integração.
- Utilize variáveis de ambiente para credenciais.
- Valide as configurações necessárias.
- Apresente mensagens claras quando o serviço não estiver disponível.
- Crie um modo de desenvolvimento claramente identificado, se necessário.
- Não simule uma geração real com uma imagem aleatória fingindo que a IA foi executada.

Não exponha chaves secretas no frontend.

---

# 5. SISTEMA DE UPLOADS E BIBLIOTECA DE MÍDIA

O usuário precisa conseguir enviar seus próprios materiais e reutilizá-los em vários projetos.

Implemente uma biblioteca de mídia com:

- Upload de imagens.
- Pré-visualização.
- Miniaturas.
- Nome do arquivo.
- Dimensões.
- Tipo de mídia.
- Data de envio.
- Busca.
- Exclusão com confirmação.
- Organização por projeto ou categoria.
- Reutilização de arquivos.
- Estados de processamento.

Aceite formatos de imagem apropriados para o produto, com validação de MIME type, tamanho máximo e conteúdo real do arquivo.

Considere PNG, JPEG, WebP e SVG apenas quando o tratamento de SVG for seguro.

Não permita que arquivos enviados sejam interpretados como código executável.

## Logos

A logo da empresa deve ser tratada como um elemento prioritário.

Quando possível:

- Preserve a transparência.
- Preserve a proporção original.
- Não distorça a marca.
- Permita redimensionamento proporcional.
- Permita reposicionamento.
- Preserve o arquivo original.
- Ofereça uma pré-visualização em fundos claros e escuros.

A IA pode sugerir posicionamentos e composições, mas não deve redesenhar automaticamente uma logo oficial sem autorização explícita.

## Imagens de referência

As imagens enviadas devem poder servir como:

- Referência estética.
- Referência de composição.
- Fotografia principal.
- Fotografia secundária.
- Referência de produto ou serviço.
- Referência de cores.
- Inspiração visual.

O sistema precisa distinguir entre uma imagem usada como referência e uma imagem efetivamente colocada na arte.

---

# 6. EDITOR GRÁFICO REAL

Implemente um editor visual baseado em elementos independentes.

A arquitetura do documento deve permitir que cada objeto seja selecionado e editado separadamente.

Tipos de elementos:

- Texto.
- Imagem.
- Logo.
- Forma geométrica.
- Ícone.
- Botão visual.
- Fundo.
- Elemento decorativo.
- Elemento vetorial, quando suportado.
- Grupo de elementos.

## Operações essenciais

- Selecionar.
- Mover.
- Redimensionar.
- Girar.
- Duplicar.
- Excluir.
- Bloquear.
- Ocultar.
- Trazer para frente.
- Enviar para trás.
- Alinhar.
- Distribuir.
- Agrupar e desagrupar.
- Editar texto.
- Alterar cores.
- Substituir imagens.

Implemente desfazer e refazer por meio de um histórico consistente de operações ou snapshots controlados do documento.

Evite salvar uma cópia completa e pesada do estado a cada movimento do mouse.

O editor deve manter a sincronização entre:

- Canvas.
- Painel de propriedades.
- Lista de camadas.
- Histórico.
- Documento persistido.

## Seleção e transformação

Os elementos selecionados devem apresentar controles visuais de transformação.

Inclua:

- Contorno de seleção.
- Alças de redimensionamento.
- Controle de rotação.
- Indicadores de alinhamento.
- Opção de bloqueio.
- Seleção múltipla, quando viável.

Garanta que os controles funcionem corretamente com zoom e diferentes tamanhos de canvas.

Não permita que um movimento na interface desloque os elementos para posições incorretas no documento.

## Implementação técnica

Escolha a tecnologia mais apropriada para a stack existente.

Avalie, conforme o contexto, bibliotecas como Fabric.js, Konva ou soluções SVG/DOM.

Não introduza uma biblioteca apenas por preferência pessoal.

A escolha precisa considerar:

- Edição interativa.
- Seleção e transformação.
- Texto.
- Imagens.
- Camadas.
- Serialização.
- Desempenho.
- Exportação em alta resolução.
- Compatibilidade com o framework existente.

Crie uma representação serializável do documento, independente de detalhes transitórios da interface.

---

# 7. SISTEMA DE CORES E TEMAS DINÂMICOS

Este recurso é essencial.

O usuário precisa conseguir transformar completamente a identidade cromática de uma arte sem precisar recriá-la.

Crie um sistema de paletas com presets e cores personalizadas.

## Paletas predefinidas

Inclua opções como:

- Laranja industrial.
- Azul corporativo.
- Verde sustentável.
- Vermelho de impacto.
- Roxo moderno.
- Preto premium.
- Azul e laranja.
- Verde e preto.
- Vermelho e branco.
- Monocromático.
- Minimalista neutro.
- Personalizado.

Cada paleta deve definir papéis semânticos, e não apenas uma lista de cores aleatórias.

Exemplo:

- `primary`: cor principal.
- `secondary`: cor secundária.
- `accent`: cor de destaque.
- `background`: fundo.
- `surface`: superfícies.
- `text`: texto principal.
- `mutedText`: texto secundário.
- `border`: bordas.
- `cta`: chamada para ação.
- `overlay`: sobreposições.

Os nomes são ilustrativos; adapte-os à convenção da arquitetura escolhida.

## Exemplo de paleta industrial

Laranja:

- Principal: `#FF5A1F`
- Secundária: `#FF8A3D`
- Fundo: `#FFFFFF`
- Texto: `#111827`
- Destaque: `#FF3D00`

Azul:

- Principal: `#1769FF`
- Secundária: `#53A0FF`
- Fundo: `#FFFFFF`
- Texto: `#111827`
- Destaque: `#0047AB`

Verde:

- Principal: `#159447`
- Secundária: `#63C174`
- Fundo: `#FFFFFF`
- Texto: `#111827`
- Destaque: `#087443`

## Aplicação das cores

A troca de paleta deve atualizar os elementos vinculados às cores sem modificar desnecessariamente:

- Conteúdo dos textos.
- Posição dos elementos.
- Dimensões.
- Hierarquia visual.
- Logo original.
- Fotografias originais.
- Informações comerciais.
- Estrutura do documento.

Permita que elementos específicos sejam desvinculados da paleta global e recebam cores personalizadas.

## Paletas personalizadas

Implemente:

- Seletor de cor.
- Entrada hexadecimal.
- Cores recentes.
- Salvamento de paletas.
- Duplicação de paletas.
- Edição de cores individuais.
- Aplicação de uma paleta a toda a arte.
- Pré-visualização antes de aplicar, se apropriado.

## Identificação de cores por IA

Quando houver uma imagem de referência, ofereça a possibilidade de extrair uma paleta aproximada dessa imagem.

A extração pode utilizar processamento de imagem local ou um serviço específico.

Não afirme que uma imagem possui cores exatas sem realizar a análise correspondente.

## Variações inteligentes

Permita gerar uma coleção de versões da mesma arte:

- Versão laranja.
- Versão azul.
- Versão verde.
- Versão escura.
- Versão minimalista.

Todas devem compartilhar a mesma base de conteúdo.

A troca de cores não deve exigir uma nova geração de imagem por IA quando puder ser resolvida por propriedades do documento.

Se uma nova geração de imagem for necessária para alterar iluminação, cenário ou composição fotográfica, ofereça essa opção separadamente.

---

# 8. SISTEMA DE TEMPLATES

Crie uma biblioteca de templates profissionais, organizados por finalidade e segmento.

Categorias sugeridas:

- Construção civil.
- Concreto usinado.
- Bombeamento de concreto.
- Locação de equipamentos.
- Polimento de pisos.
- Serviços profissionais.
- Comércio.
- Alimentação.
- Tecnologia.
- Saúde.
- Educação.
- Promoções.
- Institucional.
- Depoimentos.
- Portfólio.
- Datas comemorativas.
- Lançamentos.
- Anúncios.

Os templates devem conter composições editáveis, não apenas imagens planas.

Cada template deve definir:

- Dimensões.
- Fundo.
- Estrutura de camadas.
- Hierarquia tipográfica.
- Espaçamentos.
- Paleta inicial.
- Slots de imagem.
- Slots de logo.
- Textos iniciais.
- Campos substituíveis.
- Regras de composição.

Ao selecionar um template, o usuário deve poder substituir o conteúdo sem perder a estrutura visual.

Implemente busca, filtros, categorias, favoritos e templates recentes.

Se a biblioteca inicial for pequena, priorize templates de alta qualidade e uma arquitetura que permita adicionar novos modelos facilmente.

Não apresente templates fictícios como se fossem arquivos profissionais completos quando eles ainda não existirem.

---

# 9. FORMATOS DE PUBLICAÇÃO

O usuário precisa escolher o destino da arte antes de exportar.

Crie presets para:

| Formato | Dimensões |
|---|---:|
| Instagram Feed quadrado | 1080 × 1080 |
| Instagram Feed vertical | 1080 × 1350 |
| Instagram Stories | 1080 × 1920 |
| WhatsApp Status | 1080 × 1920 |
| Post horizontal | 1200 × 628 |
| Facebook quadrado | 1080 × 1080 |
| Formato personalizado | Largura × altura |

Os formatos devem ser configuráveis, não codificados de forma espalhada pela aplicação.

Permita criar dimensões personalizadas.

Ao alterar o formato, não estique a arte indiscriminadamente.

Implemente uma estratégia de adaptação que possa:

- Redimensionar proporcionalmente.
- Reposicionar elementos.
- Preservar margens.
- Ajustar quebras de texto.
- Alertar sobre elementos fora da área segura.
- Oferecer uma nova composição com assistência de IA.

Quando a adaptação exigir uma decisão criativa importante, permita ao usuário escolher entre manter, adaptar automaticamente ou gerar uma nova versão.

---

# 10. EXPORTAÇÃO PROFISSIONAL

A exportação é uma funcionalidade central.

O usuário precisa baixar uma arte que realmente possa utilizar nas redes sociais.

Formatos iniciais:

- PNG.
- JPEG.

Inclua SVG ou PDF apenas quando houver uma necessidade real e a implementação for compatível com os recursos utilizados no documento.

Opções:

- Dimensões de exportação.
- Qualidade para JPEG.
- Escala de renderização.
- Fundo transparente, quando aplicável.
- Nome do arquivo.
- Pré-visualização.
- Download.
- Indicação de progresso.

A exportação deve utilizar as dimensões finais do documento, independentemente do zoom do editor.

Uma arte de 1080 × 1350 deve ser exportada nessa resolução, mesmo que o canvas esteja sendo exibido a 40% na tela.

Não exporte a interface do editor junto com a arte.

Não exporte os controles de seleção, guias ou alças de transformação.

## Qualidade

- Preserve a proporção das imagens.
- Evite deformações.
- Evite imagens borradas desnecessariamente.
- Carregue fontes antes de iniciar a renderização.
- Preserve a transparência quando solicitada.
- Verifique a origem e as permissões dos recursos externos.
- Trate corretamente erros de renderização.
- Mostre uma mensagem útil quando um formato não for suportado.

Se o editor usar canvas, trate corretamente imagens de origem externa e as restrições de segurança que podem impedir a exportação.

Não declare que a exportação está pronta sem testar o arquivo gerado.

---

# 11. GERAÇÃO DE VARIAÇÕES E COMPARAÇÃO

Implemente um sistema para criar e comparar diferentes versões da mesma campanha.

Tipos de variação:

1. Variação de cores.
2. Variação tipográfica.
3. Variação de composição.
4. Variação de fotografia.
5. Variação de texto publicitário.
6. Variação de CTA.
7. Variação de formato.
8. Variação de estilo visual.

Crie uma área de comparação com miniaturas.

O usuário precisa poder:

- Visualizar as versões.
- Comparar lado a lado.
- Selecionar uma versão.
- Duplicar uma versão.
- Editar uma versão.
- Excluir uma versão.
- Definir uma versão como principal.
- Exportar uma versão individualmente.

Não substitua silenciosamente a arte original quando uma variação for criada.

Cada versão deve possuir seu próprio documento ou snapshot persistido, com referência à origem e às alterações aplicadas.

Diferencie variações determinísticas de cores das variações generativas produzidas pela IA.

---

# 12. TEXTO PUBLICITÁRIO E IDENTIDADE DA EMPRESA

A IA pode ajudar a produzir:

- Títulos.
- Subtítulos.
- Benefícios.
- Listas de diferenciais.
- Chamadas para ação.
- Legendas para redes sociais.
- Hashtags.
- Textos curtos para anúncios.

O usuário deve poder editar todos os textos gerados.

Nunca invente dados comerciais como:

- Telefone.
- Endereço.
- Preços.
- Descontos.
- Garantias.
- Certificações.
- Depoimentos reais.
- Estatísticas.
- Promessas comerciais.

Se uma informação não tiver sido fornecida, utilize um campo substituível ou omita-a.

Implemente um perfil de marca que possa armazenar, mediante autorização do usuário:

- Nome da empresa.
- Logo.
- Cores oficiais.
- Tipografias preferidas.
- Tom de voz.
- Telefone comercial.
- Site.
- Redes sociais.
- Descrição dos serviços.
- Preferências visuais.

Esse perfil poderá ser reutilizado nos próximos projetos.

Não force a inclusão de todos os dados em todas as artes.

---

# 13. HISTÓRICO, PROJETOS E SALVAMENTO

O sistema precisa preservar o trabalho do usuário.

Implemente:

- Criação de projeto.
- Renomeação.
- Duplicação.
- Salvamento.
- Autosave.
- Histórico de alterações.
- Recuperação após falhas de rede, quando viável.
- Listagem de projetos recentes.
- Miniatura do projeto.
- Data da última edição.
- Exclusão com confirmação.
- Arquivamento, se fizer sentido.
- Reabertura do documento editável.

O autosave deve ser debounced e não deve enviar uma requisição a cada alteração de posição durante o arraste de um objeto.

Trate conflitos de atualização e falhas de persistência.

Mostre estados como:

- Salvando.
- Salvo.
- Alterações pendentes.
- Falha ao salvar.

Evite informar que um projeto foi salvo quando o servidor não confirmou a operação.

---

# 14. SEGURANÇA, PRIVACIDADE E CUSTOS

Considere que a aplicação pode trabalhar com imagens privadas, logos comerciais e credenciais de serviços de IA.

Implemente:

- Autorização por projeto.
- Validação no servidor.
- Proteção dos uploads.
- Limites de tamanho e quantidade de arquivos.
- Controle de acesso aos recursos.
- Tratamento seguro de erros.
- Proteção das credenciais.
- Limites de geração.
- Registro de operações relevantes.
- Controle de custos das chamadas de IA.
- Prevenção contra solicitações repetidas acidentais.
- Exclusão de mídias e projetos conforme as regras definidas pelo produto.

Se houver processamento assíncrono, utilize filas e mecanismos de repetição com limites.

Não registre chaves secretas, conteúdo privado desnecessário ou informações sensíveis nos logs.

Não faça chamadas dispendiosas a modelos de IA automaticamente durante cada alteração visual.

Aplique as alterações determinísticas localmente sempre que possível.

---

# 15. ARQUITETURA DO DOCUMENTO GRÁFICO

Defina uma representação serializável para cada arte.

A estrutura deve permitir armazenar:

- Versão do schema.
- Identificador do projeto.
- Dimensões do canvas.
- Cor ou imagem de fundo.
- Paleta aplicada.
- Elementos.
- Ordem das camadas.
- Propriedades dos elementos.
- Referências às mídias.
- Metadados de composição.
- Configurações de exportação.
- Histórico ou referência de versão.

Cada elemento deve possuir um identificador estável.

As cores vinculadas à paleta global devem manter essa relação no documento.

As cores personalizadas devem ser preservadas quando o usuário decidir desvincular um elemento do tema.

Não armazene imagens grandes em Base64 dentro do JSON do projeto, salvo quando houver uma justificativa técnica muito específica.

Utilize referências aos arquivos armazenados.

Implemente versionamento do schema para permitir futuras mudanças na estrutura dos documentos.

Crie funções ou serviços independentes para:

- Criar documentos.
- Validar documentos.
- Clonar documentos.
- Aplicar paletas.
- Adaptar formatos.
- Serializar.
- Desserializar.
- Exportar.
- Criar variações.

---

# 16. DESEMPENHO E RESPONSIVIDADE

O workspace deve funcionar bem em computadores e oferecer uma experiência utilizável em telas menores.

Prioridades:

- Desktop: experiência completa de edição.
- Tablet: workspace adaptado.
- Mobile: navegação simplificada, ferramentas acessíveis e possibilidade de editar e exportar.

Não comprima todos os painéis do desktop em uma tela mobile.

Utilize painéis deslizantes, menus contextuais e controles adequados ao espaço disponível.

Considere:

- Carregamento progressivo de miniaturas.
- Lazy loading de imagens.
- Cache de recursos.
- Debounce em operações frequentes.
- Otimização de renderização.
- Liberação de recursos gráficos quando não forem mais necessários.
- Tratamento de documentos com muitos elementos.

A interface deve permanecer responsiva durante operações normais de edição.

---

# 17. ACESSIBILIDADE E USABILIDADE

Implemente:

- Contraste adequado.
- Estados de foco visíveis.
- Navegação por teclado onde aplicável.
- Rótulos acessíveis.
- Botões com nomes compreensíveis.
- Mensagens de erro objetivas.
- Tooltips para ícones ambíguos.
- Alvos de interação adequados.
- Confirmação para ações destrutivas.
- Estados vazios explicativos.

Não utilize somente cores para comunicar sucesso, erro ou seleção.

Evite interfaces que dependam de o usuário descobrir funcionalidades escondidas sem indicação visual.

---

# 18. ESTRUTURA DAS TELAS

Implemente as seguintes áreas, respeitando a arquitetura já existente.

## Tela A — Dashboard

Deve conter:

- Botão para criar nova arte.
- Projetos recentes.
- Templates em destaque.
- Acesso à biblioteca de mídia.
- Acesso à identidade visual.
- Atalhos para formatos populares.

## Tela B — Criar com IA

Deve conter:

- Briefing.
- Upload de imagens.
- Upload de logo.
- Paletas.
- Formatos.
- Estilos.
- Botão de geração.
- Estados de processamento.
- Resultado com opção de editar.

## Tela C — Workspace

Deve conter:

- Barra superior.
- Ferramentas laterais.
- Canvas.
- Painel de propriedades.
- Camadas.
- Histórico.
- Paletas.
- Variações.
- Salvamento.
- Exportação.

## Tela D — Biblioteca de projetos

Deve conter:

- Miniaturas.
- Busca.
- Filtros.
- Ordenação.
- Duplicação.
- Exclusão.
- Reabertura para edição.

## Tela E — Identidade visual

Deve permitir:

- Gerenciar logos.
- Criar paletas.
- Salvar cores oficiais.
- Configurar informações comerciais.
- Definir preferências de tipografia.
- Gerenciar imagens recorrentes.

## Tela F — Biblioteca de mídia

Deve permitir visualizar, pesquisar, enviar, reutilizar e remover imagens autorizadas pelo usuário.

---

# 19. DETALHES DE UX QUE NÃO PODEM SER IGNORADOS

Implemente interações reais.

Exemplos:

- Clicar em uma paleta altera efetivamente as cores vinculadas.
- Clicar em uma camada seleciona o elemento correspondente.
- Ocultar uma camada remove o elemento da visualização sem excluí-lo.
- Bloquear uma camada impede modificações acidentais.
- Duplicar um elemento cria uma nova instância independente.
- Alterar um texto atualiza o canvas.
- Desfazer reverte a última operação relevante.
- Refazer restaura uma operação desfeita.
- Salvar persiste o documento.
- Reabrir restaura a composição.
- Exportar gera um arquivo real.
- Selecionar um formato adapta o canvas.
- Gerar uma variação cria uma nova versão.
- Trocar a imagem substitui a mídia vinculada sem apagar o restante da composição.

Não entregue botões que apenas mudam de cor ou mostram notificações sem realizar suas ações.

Se um recurso não puder ser implementado completamente, deixe-o explicitamente identificado como indisponível ou pendente. Não simule seu funcionamento.

---

# 20. TESTES E GARANTIA DE QUALIDADE

Crie testes proporcionais à arquitetura e aos riscos do produto.

Inclua testes para:

- Criação de projetos.
- Persistência de documentos.
- Validação dos elementos.
- Aplicação de paletas.
- Preservação de cores personalizadas.
- Duplicação de documentos.
- Adaptação de formatos.
- Permissões.
- Uploads.
- Exclusão.
- Exportação.
- Histórico.
- Integrações com IA.
- Tratamento de falhas.
- Estados de salvamento.

Para o frontend, teste os comportamentos interativos críticos.

Se houver uma infraestrutura de testes de navegador, cubra o fluxo principal:

1. Criar um projeto.
2. Selecionar um template.
3. Adicionar ou substituir uma imagem.
4. Alterar uma paleta.
5. Editar um texto.
6. Salvar.
7. Reabrir.
8. Exportar.
9. Validar a existência e as dimensões do arquivo exportado.

Execute os testes disponíveis no repositório e corrija as regressões causadas pelas alterações.

Não afirme que os testes passaram se não os tiver executado.

---

# 21. ORDEM DE IMPLEMENTAÇÃO

Implemente o produto por etapas, priorizando uma experiência utilizável de ponta a ponta.

## Fase 1 — Fundação

- Análise do projeto.
- Definição da arquitetura.
- Estrutura do documento gráfico.
- Organização do workspace.
- Persistência.
- Identificação das dependências necessárias.

## Fase 2 — Editor funcional

- Canvas.
- Texto.
- Imagens.
- Formas.
- Seleção.
- Transformação.
- Camadas.
- Desfazer e refazer.

## Fase 3 — Identidade visual

- Paletas.
- Cores personalizadas.
- Vínculos semânticos de cor.
- Troca instantânea de temas.
- Biblioteca de mídia.
- Upload de logo.

## Fase 4 — Templates e formatos

- Biblioteca de templates.
- Categorias.
- Formatos de redes sociais.
- Adaptação de layout.
- Modelos reutilizáveis.

## Fase 5 — IA

- Briefing.
- Integração com provedores.
- Geração de imagens.
- Composição editável.
- Variações.
- Estados de processamento.

## Fase 6 — Exportação

- PNG.
- JPEG.
- Resolução final.
- Pré-visualização.
- Tratamento de falhas.
- Testes de integridade.

## Fase 7 — Refinamento

- Responsividade.
- Acessibilidade.
- Desempenho.
- Testes de regressão.
- Refinamento visual.
- Documentação.

Você pode reorganizar essas fases se a análise técnica demonstrar uma dependência diferente. Entretanto, não deixe o editor real para depois de construir várias telas decorativas.

**Priorize um fluxo completo que funcione, mesmo que inicialmente tenha menos recursos, em vez de uma enorme interface com funcionalidades falsas.**

---

# 22. CRITÉRIOS DE ACEITAÇÃO

Considere a primeira versão funcional quando o usuário conseguir:

1. Criar um projeto.
2. Selecionar um template editável.
3. Enviar uma logo.
4. Enviar uma imagem.
5. Inserir e editar texto.
6. Mover e redimensionar elementos.
7. Alterar as cores globais.
8. Criar uma paleta personalizada.
9. Gerar uma variação de cores.
10. Selecionar um formato de publicação.
11. Salvar e reabrir o projeto.
12. Exportar uma imagem real em PNG ou JPEG.
13. Encontrar e reutilizar mídias enviadas.
14. Utilizar a integração com IA quando estiver configurada.
15. Receber uma mensagem adequada quando uma operação falhar.

Os critérios devem ser validados com testes e verificação prática, não apenas por inspeção visual do código.

---

# 23. PADRÕES DE QUALIDADE DO CÓDIGO

- Utilize nomes claros e consistentes.
- Mantenha as responsabilidades separadas.
- Evite componentes gigantescos.
- Evite duplicação de regras de negócio.
- Centralize a lógica de aplicação de paletas.
- Centralize os presets de formato.
- Valide os documentos antes de persistir.
- Utilize tipagem adequada quando a stack permitir.
- Trate erros explicitamente.
- Preserve a segurança do servidor.
- Evite dependências circulares.
- Evite abstrações desnecessárias.
- Documente decisões arquiteturais importantes.
- Atualize a documentação de configuração.
- Não desative testes existentes para fazer a implementação passar.
- Não introduza dados fictícios em produção.

Se houver decisões importantes com múltiplas alternativas, escolha a opção mais adequada à arquitetura real, documente brevemente a decisão e continue.

Não interrompa o desenvolvimento para perguntar sobre cada detalhe que possa ser resolvido com julgamento técnico.

---

# 24. ENTREGA FINAL

Ao concluir, apresente um relatório objetivo contendo:

1. O que foi implementado.
2. Quais telas e componentes foram criados ou modificados.
3. Como funciona o documento editável.
4. Como as paletas são aplicadas.
5. Como funciona a exportação.
6. Como configurar os provedores de IA.
7. Quais variáveis de ambiente são necessárias.
8. Quais migrations, comandos ou configurações precisam ser executados.
9. Quais testes foram executados e seus resultados.
10. Quais limitações ainda existem.

Diferencie claramente recursos completos, recursos parciais e integrações que dependem de configuração externa.

Não declare a entrega completa se os critérios de aceitação ainda não tiverem sido verificados.

---

# INSTRUÇÃO FINAL

Comece inspecionando o repositório e identificando a arquitetura real.

Em seguida, defina o plano técnico mais apropriado e implemente o produto diretamente no projeto existente.

Construa uma aplicação com qualidade de produto comercial: visual refinado, editor funcional, identidade visual dinâmica, IA integrada de maneira responsável e exportação confiável.

O objetivo final não é apenas gerar imagens bonitas.

**O objetivo é permitir que qualquer empresa crie, personalize, versione e exporte seus próprios materiais gráficos profissionais em poucos minutos, sem depender de um designer para cada alteração.**