<?php
    // cabeçalho
    header("Conten-Type: application/json"); // defie o tipo de resposta

    $metodo = $_SERVER['REQUEST_METHOD'];

    // echo "Método da requisição: ".$metodo;

    //RECUPRA O ARQUIVO JSON NA MESMA PASTA DO PROJETO
    $arquivo = 'usuarios.json';

    //  VERIFICA SE O ARQUIVO EXISTE, SE NÃO EXISTIR CRIA UM COM ARRAY VAZIO
    if(!file_exists($arquivo)){
        file_put_contents($arquivo, json_encode([],JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
    
    // LÊ O CONTEÚO ARQUIVO JSON
    $usuarios = json_decode(file_get_contents($arquivo), true);
    // conteudo
    // $usuarios = [
    //     ["id" => 1, "nome"=>"Maria Souza", "email" => "maria@email.com"],
    //     ["id" => 2, "nome" => "João Silva", "email" => "joao@email.com"]
    // ];

    switch ($metodo){
        case 'GET':
            // Verifica se há um parâmetro "id" na URL
            if (isset($_GET['id'])){
                $id = intval($_GET['id']);
                $usuario_encontrado = null;

                // Procura o usuário pelo ID
                foreach ($usuarios as $usuario) {
                    if ($usuario ['id'] == $id) {
                        $usuario_encontrado = $usuario ;
                        break;
                    }
                }
                if ($usuario_encontrado){
                    echo json_encode($usuario_encontrado, JSON_PRETTY_PRINT |JSON_UNESCAPED_UNICODE);
                } else {
                    http_response_code(404);
                    echo json_encode(["erro" => "Usuário não encontrado."], JSON_UNESCAPED_UNICODE);
                }
            } else {
                // Retorna todos os usuarios
                echo json_encode($usuarios, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            } 
            break;

        case 'POST':
            // echo "AQUI AÇÕES DO MÉTODO POST";
            // LER OS DADOS NO CORPO DA REQUISIÇÃO
            $dados = json_decode(file_get_contents('php://input'),true);
            // print_r($dados);
            // VERIFICA SE OS CAMPOS OBRIGATÓRIOS FORAM PREENCHIDOS
            if (!isset($dados["id"])|| !isset($dados["nome"])|| !isset($dados["email"])) {
                http_response_code(400);
                echo json_encode(["erro"=> "Dados incompletos."], JSON_UNESCAPED_UNICODE);
                exit;
            }
            // GERA UM NOVO ID ÚNICO
            $novo_id = 1;
            if (!empty($usuarios)) {
                $ids = array_column($usuarios, 'id');
                $novo_id = max($ids) + 1;
            }

            // CRIA NOVO USUÁRIO
            $novoUsuario = [
                "id" => $dados["id"],
                "nome" => $dados["nome"],
                "email" => $dados ["email"]
            ];
            // ADICIONA AO ARRAY DE USUÁRIOS
            $usuarios[] = $novo_usuario;

            // SALVA O ARRAY ATUALIZADO NO ARQUIVA JSON
            file_put_contents ($arquivo, json_encode($usuarios, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            // RETORNA MENSAGEM DE SUCESSO 
            echo json_encode(["mensagem"=> "Usuário inserido com sucesso!", 
            "usuarios" => $usuarios], JSON_UNESCAPED_UNICODE);

            // Adiciona o novo usuário ao array existente
            // array_push($usuarios, $novoUsuario);
            // echo json_encode('Usuário inserido com sucesso!');
            // print_r($usuarios);

            break;

        default:
            // echo "MÉTODO NÃO ECONTRADO";
            // break;
            http_response_code(405); // Método não permitido
            echo json_encode (["erro"=> "Médodo não permitido"], JSON_UNESCAPED_UNICODE);
            break;
            
    }
?>