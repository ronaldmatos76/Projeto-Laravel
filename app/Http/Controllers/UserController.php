<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use Validator;
use Response;
//use DataTables;
use DB;
use Auth;
use App\User;
use Hash;

class UserController extends Controller
{
    //
    
    public function index()
    {
       
        return view('usuario.index');
    }
    //Função para criar botões 
   /* private function setDataButtons(User $usuario){
    
        //Variável de status
        if($usuario->status)
            $status = 'Ativo';
        else
            $status = 'Inativo';
        
        foreach($usuario->getRoleNames() as $tipoFuncao){
            $funcao = $tipoFuncao;
        }
            $dados = 'data-nome="'.$usuario->name.'" data-email="'.$usuario->email.'" data-telefone="'.$usuario->telefone.'" data-funcao="'.$funcao.'"
                data-endereco="'.$usuario->endereco.'" data-cidade="'.$usuario->cidade->id .'" data-estado="'.$usuario->cidade->estado->id.'"
                data-status="'.$status.'"';
            $dados_visualizar = 'data-nome="'.$usuario->name.'" data-email="'.$usuario->email.'" data-telefone="'.$usuario->telefone.'" data-funcao="'.$funcao.'"
                data-endereco="'.$usuario->endereco.'" data-cidade="'.$usuario->cidade->nome .'" data-estado="'.$usuario->cidade->estado->nome.'"
                data-status="'.$status.'"';
            $btnVisualizar = '<a class="btn btn-info btnVisualizar" '. $dados_visualizar .' title="Visualizar" data-toggle="tooltip"><i class="fa fa-eye"></i></a>';
            $btnEditar = ' <a data-id="'.$usuario->id.'" class="btn btn-primary btnEditar" '. $dados .' title="Editar" data-toggle="tooltip"><i class="fa fa- fa-pencil-square-o"></i></a>';
            if(Auth::user()->id == $usuario->id)
                $btnExcluir = '';
            else
            $btnExcluir = ' <a data-id="'.$usuario->id.'" class="btn btn-danger btnExcluir" title="Desativar" data-toggle="tooltip"><i class="fa fa-trash-o"></i></a>';
            //caso status do úsuario seja inativo
            if(!$usuario->status){
                $btnAtivar = ' <a data-id="'.$usuario->id.'" class="btn btn-warning btnAtivar" '. $dados .' title="Ativar Usúário" data-toggle="tooltip" ><i class="fa fa-user-plus"> </i></a>';
                return $btnVisualizar.$btnEditar.$btnAtivar;
            }else{
                return $btnVisualizar.$btnEditar.$btnExcluir;
            }
        }
        //Função listar
        public function listar(){
            $usuario = User::all();
            
            //dd($user->getPermissionsViaRoles());
            return Datatables::of($usuario)
            ->editColumn('acao',function($usuario){
                return $this->setDataButtons($usuario);
            })
            ->editColumn('status',function($usuario){
                if($usuario->status)
                    return " <span class='label label-success' style='font-size:14px'>Ativo</span>";
                else
                    return " <span class='label label-default' style='font-size:14px'>Inativo</span>";
            })
            ->editColumn('funcao', function($usuario){
                foreach($usuario->getRoleNames() as $tipoFuncao){
                    $funcao = $tipoFuncao;
                }
                return $funcao;
            })
            ->escapeColumns([0])
            ->make(true);
        }
    //função para Cadastrar usuários
    public function store(Request $request){
        
        $rules = array(
            'nome' => 'required',
            'email' => 'required|email|unique:users,email',
            'senha' => 'required|min:8|same:confirmarsenha',
            'endereco' => 'required',
            'telefone' => 'required',
            'cidade' => 'required',
            'estado' => 'required',
            'funcao' => 'required',
        );
        $attributeNames = array(
            'confirmarsenha' => 'confirmar senha',
            'funcao' => 'função',
            'nome' => 'Nome Completo' 
        );
        $messages = array(
            'same' => 'Essas senhas não coincidem.',
        );
        $validator = Validator::make(Input::all(), $rules, $messages);
        $validator->setAttributeNames($attributeNames);
        if ($validator->fails()){
            return Response::json(array('errors' => $validator->getMessageBag()->toArray()));
        }else{
            //recuperando dados do formulário e salvando no banco
            $usuario = new User();
            $usuario->name = $request->nome;
            $usuario->email = $request->email;
            $usuario->password = Hash::make($request->senha);
            $usuario->telefone = $request->telefone;
            $usuario->endereco = $request->endereco;
            $usuario->fk_cidade = $request->cidade;
            $usuario->status = true;
            $usuario->save();
            //setando o tipo de papel ao usuário
            $usuario->assignRole($request->funcao);
            
           // Herdando as permissões via roles
           // foreach ($usuario->getPermissionsViaRoles() as $permissoes) {
            //    $usuario->givePermissionTo($permissoes);   
           // }
            
            $usuario->setAttribute('buttons', $this->setDataButtons($usuario));
            return response()->json($usuario);
        }
    }
    //Função para atualizar dados do Usuário
    public function update(Request $request){ 
        $rules = array(
            'nome' => 'required',
            'telefone' => 'required',
            'endereco' => 'required',
            'cidade' => 'required',
            'estado' => 'required'
        );
        $attributeNames = array(
            'nome' => 'Nome Completo'
        );
        $validator = Validator::make(Input::all(), $rules);
        
        if($validator->fails()){
            return Response::json(array('errors' => $validator->getMessageBag()->toArray()));
        }else{
            $usuario = User::find($request->id);
            
            foreach($usuario->getRoleNames() as $funcao)
                $role = $funcao;
            
            $usuario->removeRole($role);
            $usuario->name = $request->nome;
            $usuario->email = $request->email;
            $usuario->telefone = $request->telefone;
            $usuario->endereco = $request->endereco;
            $usuario->fk_cidade = $request->cidade;
            $usuario->assignRole($request->funcao);
            $usuario->save();
            $usuario->setAttribute('buttons',$this->setDataButtons($usuario));
            return response()->json($usuario);
        }
    }
    //desativar Funcionário
    public function destroy(Request $request){
        if(Auth::user()->id != $request->id){
            $usuario = User::find($request->id);
            $usuario->status = false;      
            $usuario->save();
            return response()->json($usuario);
        }
        return false;
    }
    //Ativar Usuário
    public function ativar(Request $request){
        $usuario = User::find($request->id);
        $usuario->status = true;
        $usuario->save();
        return response()->json($usuario);
    }
    //Select Cidade
    public function selectCidade(Request $request){
        //consulta no banco
        $dados_cidades = Cidade::where('fk_estado',$request->estado)
        ->select('id','nome')
        ->orderBy('nome')
        ->get();
        //Array de cidade
        $cidades = array();
        foreach($dados_cidades as $dados_cidade){
            array_push($cidades,[
                'id' => $dados_cidade->id,
                'nome' => $dados_cidade->nome
            ]);
        }
        //retornando para o javascript
        return response()->json(['cidades' => $cidades]);
        
    } */
}