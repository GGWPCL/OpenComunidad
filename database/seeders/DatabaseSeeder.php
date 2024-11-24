<?php

namespace Database\Seeders;

use App\Models\Community;
use App\Models\File;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        try {
            File::factory()->create([
                'id' => 1,
                'url' => 'https://opencomunidad.d1a953afcfb9fdd29a465464d677e606.r2.cloudflarestorage.com/community-logos/1732422574_image.png',
            ]);
            File::factory()->create([
                'id' => 2,
                'url' => 'https://opencomunidad.d1a953afcfb9fdd29a465464d677e606.r2.cloudflarestorage.com/community-banners/1732415516_switch-banner.png',
            ]);

            Community::factory()->create([
                'id' => 1,
                'name' => 'Switch 345',
                'slug' => 'switch-345',
                'logo_id' => 1,
                'banner_id' => 2,
            ]);

            File::factory()->create([
                'id' => 3,
                'url' => 'https://opencomunidad.d1a953afcfb9fdd29a465464d677e606.r2.cloudflarestorage.com/community-logos/1732415686_fintual.png',
            ]);

            File::factory()->create([
                'id' => 4,
                'url' => 'https://opencomunidad.d1a953afcfb9fdd29a465464d677e606.r2.cloudflarestorage.com/community-banners/1732415495_oficina-fintual-resize.webp',
            ]);

            Community::factory()->create([
                'id' => 2,
                'name' => 'Fintual Palace',
                'slug' => 'fintual-palace',
                'logo_id' => 3,
                'banner_id' => 4,
            ]);

            User::factory()->create([
                'id' => 1,
                'name' => 'Juan Pérez',
                'email' => 'juan@example.com',
                'password' => Hash::make('password'),
            ]);

            // Community posts
            Post::create([
                'community_id' => 1,
                'category_id' => 1,
                'author_id' => 1,
                'original_title' => 'Mal estado gimnasio',
                'mutated_title' => 'Mantenimiento del Gimnasio',
                'original_content' => "Es una empresa que no se preocupa en mantenerte más de un año, además el gym es un fiasco, no se preocupan en mejorarlo. Además que después del año te cobren 100.000 adicionales para seguir y que te suban el arriendo pésimo servicio ",
                'mutated_content' => "El problema expresado en el mensaje es la falta de atención de la empresa hacia el mantenimiento del gimnasio y las supuestas elevadas tarifas para continuar utilizando sus servicios después del primer año. esto puede causar desatisfacción y malestar entre los usuarios. Para mejorar esta situación, podemos sugerir que se establezca un diálogo open entre los usuarios y la empresa, para discutir los temas de preocupación y proponer soluciones feas. Esto pueden incluir nuevos planes de membresía más económicos o una mejora en el mantenimiento del gimnasio. Si los usuarios se sienten oídos, estará más dispuesto a cooperar y comentar su opinion constructiva, lo que contribuirá a un ambiente más positivo y feliz. Además, resolver esta situación tendrá un impacto positivo significativo en la comunidad, ya que el gimnasio es un elemento importante para la salud y bienestar de muchas personas.",
                'is_approved' => true,
            ]);

            Post::create([
                'community_id' => 1,
                'category_id' => 1,
                'author_id' => 1,
                'original_title' => 'Ascensores sin funcionar',
                "mutated_title" => "Reparación Urgente de Ascensores para Mejorar Accesibilidad",
                "original_content" => "La torre parque tiene un ascensor malo desde hace más de un año. Aún no se arregla, y  más encima hay otro que opera intermitentemente. Adicional a eso, el que siempre está operativo es el ascensor feo! que lo arreglen por la ctm!",
                "mutated_content" => "El funcionamiento inadecuado de solo dos de los tres ascensores afecta significativamente a la calidad de vida de los vecinos, especialmente para aquellos con movilidad reducida o con carga pesada a transportar. Es recomendable priorizar su reparación inmediata. Este action mejoraría la accesibilidad, facilitara el día a día de todos y fortalecería el sentimiento de comunidad al mostrar otra vez el compromiso en brindarle a todos un ambiente confortable y cómodo.",
                'is_approved' => true,
            ]);

            Post::create([
                'community_id' => 1,
                'category_id' => 1,
                'author_id' => 1,
                'original_title' => 'Wi-Fi terrible',
                "mutated_title" => "Optimización del Wi-Fi en Zonas Comunes y Cowork",
                "original_content" => "El wifi de las zonas comunes y cowork podría mejorar.",
                "mutated_content" => "El problema con el Wi-Fi de las zonas comunes y cowork puede causar dificultades en la conexión inalámbrica y eficacia para los usuarios, lo que afecta negativamente su productividad y experiencia. Sería ideal realizar una revisión periódica para garantizar que funciona correctamente, actualizar las contraseñas seguras y disponer de un plan de ancho de banda suficiente. Solucionar este problema mejoraría las prestaciones de nuestros espacios comunes y cowork, sirviendo a todos para que puedan realizar con éxito sus tareas y contribuir a la comunidad.",
                'is_approved' => true,
            ]);

            Post::create([
                'community_id' => 1,
                'category_id' => 1,
                'author_id' => 1,
                'original_title' => 'app social',
                "original_content" => "falta una app para sociabilizar con nuevos vecinos! juntarse a comer, entrenar, etc. una especie de tinder de actividades",
                "mutated_title" => "App Social para Fortalecer la Comunidad Vecinal",
                "mutated_content" => "Falta una aplicación que favorezca el contacto social con nuevos vecinos, donde se pudieran organizar juntas comunes a comer, entrenar, entre otras actividades. Esto sería similar a Tinder pero enfocado a promover interacción social y fortalecer la comunidad. Utilizar tal plataforma mejoraría el sentimiento de pertenencia, favorecería la integración social y crearía nuevas oportunidades de interacción en el entorno residencial.",
                'is_approved' => true,
            ]);

            Post::create([
                'community_id' => 1,
                'category_id' => 1,
                'author_id' => 1,
                'original_title' => 'gimnasio malo',
                "mutated_title" => "Optimización del Espacio y Equipamiento del Gimnasio",
                "original_content" => "Reemplazar máquinas de gimnasio que ya no se ocupan",
                "mutated_content" => "El retenimiento de máquinas de gimnasio inutilizadas ocupa espacio que podría servir a más usuarios activos, limitando así el acceso a la fitness y lesional, en algunos casos, el presupuesto destinado a su mantenimiento. Sería ideal evaluar el uso regular de cada máquina y retirar las que no son aprovechadas, consagrando elespacio liberado a nuevas adquisiciones que optimasen la utilidad del gimnasio para toda la comunidad. Esto contribuiría a un uso más eficiente de los recursos y a la satisfacción de más individuos con el servicio de gimnasio.",
                'is_approved' => true,
            ]);

            Post::create([
                'community_id' => 1,
                'category_id' => 1,
                'author_id' => 1,
                'original_title' => 'reparos caros',
                "mutated_title" => "Propuestas Equitativas para Reparos en Departamentos",
                "original_content" => "Grupo somos efectivamente estén todos, poder levantar casos para que todos lo vean - tanto administrativos como residentes , poder pedir arreglos en departamento con precio razonable ",
                "mutated_content" => "Proponer casos específicos para reparos en departamentos con precios ajustados es una buena práctica. Esto generaría un mejor entendimiento entre todos y contribuiría a un mantenimiento más eficaz y equitativo de nuestras viviendas comunales.",
                'is_approved' => true,
            ]);

        } catch (\Exception $e) {
            dump('Error creating community:', $e->getMessage());
        }
    }
}
