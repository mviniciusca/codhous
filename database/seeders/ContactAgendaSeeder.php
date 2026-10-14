<?php

namespace Database\Seeders;

use App\Models\ContactAgenda;
use App\Models\ContactInteraction;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ContactAgendaSeeder extends Seeder
{
    public function run(): void
    {
        ContactInteraction::query()->delete();
        ContactAgenda::query()->delete();

        $featured = [
            [
                'name' => 'Thiago Santos',
                'position' => 'Diretor Comercial',
                'company' => 'Concreto Forte LTDA',
                'category' => ContactAgenda::CATEGORY_CLIENT,
                'phone' => '(21) 99876-5432',
                'email' => 'thiago@concretoforte.com.br',
                'last_contact_at' => Carbon::parse('2026-10-12 14:30:00'),
                'status' => ContactAgenda::STATUS_ACTIVE,
                'is_favorite' => true,
                'interaction' => [
                    'title' => 'Reunião com Concreto Forte',
                    'type' => ContactInteraction::TYPE_MEETING,
                    'scheduled_at' => Carbon::parse('2026-10-12 10:00:00'),
                ],
            ],
            [
                'name' => 'Mariana Costa',
                'position' => 'Compradora',
                'company' => 'Obras Rio Sul',
                'category' => ContactAgenda::CATEGORY_CLIENT,
                'phone' => '(21) 98765-4321',
                'email' => 'mariana@riossul.com.br',
                'last_contact_at' => Carbon::parse('2026-10-11 10:15:00'),
                'status' => ContactAgenda::STATUS_ACTIVE,
                'is_favorite' => true,
                'interaction' => [
                    'title' => 'Enviar proposta',
                    'type' => ContactInteraction::TYPE_EMAIL,
                    'scheduled_at' => Carbon::parse('2026-10-12 16:30:00'),
                ],
            ],
            [
                'name' => 'Rodrigo Ferreira',
                'position' => 'Engenheiro',
                'company' => 'Construtora Alpha',
                'category' => ContactAgenda::CATEGORY_LEAD,
                'phone' => '(21) 97654-3210',
                'email' => 'rodrigo@alpha.com.br',
                'last_contact_at' => Carbon::parse('2026-10-10 16:20:00'),
                'status' => ContactAgenda::STATUS_IN_CONTACT,
                'is_favorite' => false,
                'interaction' => [
                    'title' => 'Reunião técnica',
                    'type' => ContactInteraction::TYPE_MEETING,
                    'scheduled_at' => Carbon::parse('2026-10-13 09:00:00'),
                ],
            ],
            [
                'name' => 'Lucas Almeida',
                'position' => 'Gerente de Obras',
                'company' => 'Projeto Horizonte',
                'category' => ContactAgenda::CATEGORY_LEAD,
                'phone' => '(21) 96543-2109',
                'email' => 'lucas@horizonte.com.br',
                'last_contact_at' => Carbon::parse('2026-10-09 11:45:00'),
                'status' => ContactAgenda::STATUS_PENDING,
                'is_favorite' => false,
                'interaction' => [
                    'title' => 'Ligação de acompanhamento',
                    'type' => ContactInteraction::TYPE_CALL,
                    'scheduled_at' => Carbon::parse('2026-10-12 14:00:00'),
                ],
            ],
            [
                'name' => 'Carla Benevides',
                'position' => 'Administrativo',
                'company' => 'Prefeitura Municipal',
                'category' => ContactAgenda::CATEGORY_CLIENT,
                'phone' => '(21) 95432-1098',
                'email' => 'carla@prefeitura.gov.br',
                'last_contact_at' => Carbon::parse('2026-10-08 09:10:00'),
                'status' => ContactAgenda::STATUS_ACTIVE,
                'is_favorite' => false,
            ],
            [
                'name' => 'Paulo Silva',
                'position' => 'Proprietário',
                'company' => 'Silva Terraplanagem',
                'category' => ContactAgenda::CATEGORY_LEAD,
                'phone' => '(21) 94321-0987',
                'email' => 'paulo@silvaterras.com.br',
                'last_contact_at' => Carbon::parse('2026-10-07 15:00:00'),
                'status' => ContactAgenda::STATUS_NO_RESPONSE,
                'is_favorite' => false,
            ],
            [
                'name' => 'Ana Mendes',
                'position' => 'Compradora',
                'company' => 'Mendes Engenharia',
                'category' => ContactAgenda::CATEGORY_CLIENT,
                'phone' => '(21) 93210-9876',
                'email' => 'ana@mendeseng.com.br',
                'last_contact_at' => Carbon::parse('2026-10-06 13:25:00'),
                'status' => ContactAgenda::STATUS_ACTIVE,
                'is_favorite' => false,
            ],
            [
                'name' => 'João Victor',
                'position' => 'Orçamentista',
                'company' => 'Base Construtora',
                'category' => ContactAgenda::CATEGORY_SUPPLIER,
                'phone' => '(21) 92109-8765',
                'email' => 'joao@base.com.br',
                'last_contact_at' => Carbon::parse('2026-10-05 10:40:00'),
                'status' => ContactAgenda::STATUS_ACTIVE,
                'is_favorite' => false,
            ],
            [
                'name' => 'Pedro Rocha',
                'position' => 'Diretor',
                'company' => 'Rocha & Associados',
                'category' => ContactAgenda::CATEGORY_PARTNER,
                'phone' => '(21) 91098-7654',
                'email' => 'pedro@rocha.com.br',
                'last_contact_at' => Carbon::parse('2026-10-04 14:15:00'),
                'status' => ContactAgenda::STATUS_IN_NEGOTIATION,
                'is_favorite' => false,
            ],
            [
                'name' => 'Fernanda Brito',
                'position' => 'Planejamento',
                'company' => 'Construtora Beta',
                'category' => ContactAgenda::CATEGORY_LEAD,
                'phone' => '(21) 90987-6543',
                'email' => 'fernanda@beta.com.br',
                'last_contact_at' => Carbon::parse('2026-10-03 09:50:00'),
                'status' => ContactAgenda::STATUS_PENDING,
                'is_favorite' => false,
            ],
        ];

        foreach ($featured as $data) {
            $interaction = $data['interaction'] ?? null;
            unset($data['interaction']);

            $contact = ContactAgenda::create($data);

            if ($interaction) {
                ContactInteraction::create([
                    'contact_agenda_id' => $contact->id,
                    'title' => $interaction['title'],
                    'type' => $interaction['type'],
                    'scheduled_at' => $interaction['scheduled_at'],
                    'status' => ContactInteraction::STATUS_PENDING,
                ]);
            }
        }

        // Complete counts to match screenshot exactly: 32 Clientes, 11 Leads, 5 Fornecedores (Total 48)
        // Currently created: 4 Clientes, 4 Leads, 1 Fornecedor, 1 Parceiro.
        // Let's create 28 more Clientes, 7 more Leads, 4 more Fornecedores:
        $clientNames = [
            'Gabriel Souza', 'Beatriz Lima', 'Rafael Moreira', 'Juliana Castro', 'Marcelo Vieira',
            'Camila Rocha', 'Bruno Carvalho', 'Larissa Duarte', 'Diego Fernandes', 'Patricia Nogueira',
            'Leonardo Martins', 'Aline Ribeiro', 'Gustavo Pires', 'Renata Farias', 'Felipe Guimarães',
            'Vanessa Azevedo', 'Eduardo Ramos', 'Priscila Dias', 'Daniel Monteiro', 'Tatiane Rezende',
            'Thiago Barbosa', 'Jessica Freitas', 'Alexandre Macedo', 'Sabrina Borges', 'Vinicius Paiva',
            'Helena Fontes', 'Caio Siqueira', 'Leticia Cardoso',
        ];

        foreach ($clientNames as $index => $name) {
            $contact = ContactAgenda::create([
                'name' => $name,
                'position' => 'Engenheiro / Responsável',
                'company' => 'Empreendimentos '.($index + 1).' SA',
                'category' => ContactAgenda::CATEGORY_CLIENT,
                'phone' => '(21) 98000-'.str_pad((string) ($index + 1000), 4, '0', STR_PAD_LEFT),
                'email' => strtolower(str_replace(' ', '.', $name)).'@obra.com.br',
                'last_contact_at' => Carbon::parse('2026-10-01')->addDays($index % 10)->setTime(rand(9, 17), rand(10, 50)),
                'status' => ContactAgenda::STATUS_ACTIVE,
                'is_favorite' => $index < 3,
            ]);

            // Add occasional calendar events
            if ($index % 5 === 0) {
                $days = [4, 7, 8, 11, 16, 17, 22, 23, 28, 30];
                $day = $days[$index % count($days)];
                $types = [ContactInteraction::TYPE_MEETING, ContactInteraction::TYPE_CALL, ContactInteraction::TYPE_EMAIL, ContactInteraction::TYPE_OTHER];
                ContactInteraction::create([
                    'contact_agenda_id' => $contact->id,
                    'title' => 'Contato com '.$name,
                    'type' => $types[$index % count($types)],
                    'scheduled_at' => Carbon::create(2026, 10, $day, rand(9, 17), 0),
                    'status' => ContactInteraction::STATUS_PENDING,
                ]);
            }
        }

        $leadNames = [
            'Marcos Antunes', 'Fabiana Peixoto', 'Sergio Mattos', 'Claudia Tavares',
            'Ronaldo Viana', 'Simone Campos', 'Mauricio Albuquerque',
        ];

        foreach ($leadNames as $index => $name) {
            ContactAgenda::create([
                'name' => $name,
                'position' => 'Gerente de Compras',
                'company' => 'Construtora Nova Era '.($index + 1),
                'category' => ContactAgenda::CATEGORY_LEAD,
                'phone' => '(21) 97000-'.str_pad((string) ($index + 2000), 4, '0', STR_PAD_LEFT),
                'email' => strtolower(str_replace(' ', '.', $name)).'@lead.com.br',
                'last_contact_at' => Carbon::parse('2026-10-02')->addDays($index % 8)->setTime(rand(9, 17), rand(10, 50)),
                'status' => $index % 2 === 0 ? ContactAgenda::STATUS_IN_CONTACT : ContactAgenda::STATUS_PENDING,
                'is_favorite' => false,
            ]);
        }

        $supplierNames = [
            'Cimento Nacional SA', 'Pedreira Santa Isabel', 'Aditivos Químicos Brasil', 'Areias & Britas Guanabara',
        ];

        foreach ($supplierNames as $index => $name) {
            ContactAgenda::create([
                'name' => $name,
                'position' => 'Atendimento Comercial',
                'company' => $name,
                'category' => ContactAgenda::CATEGORY_SUPPLIER,
                'phone' => '(21) 96000-'.str_pad((string) ($index + 3000), 4, '0', STR_PAD_LEFT),
                'email' => 'comercial@'.strtolower(preg_replace('/[^a-zA-Z]/', '', $name)).'.com.br',
                'last_contact_at' => Carbon::parse('2026-09-28')->addDays($index)->setTime(rand(9, 17), rand(10, 50)),
                'status' => ContactAgenda::STATUS_ACTIVE,
                'is_favorite' => false,
            ]);
        }
    }
}
