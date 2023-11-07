<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Menu\MenuSection;
use App\Models\Department as DepartmentModel;
use App\Nova\Address;
use App\Nova\Course;
use App\Nova\Dashboards\Main;
use App\Nova\Group;
use App\Nova\Position;
use App\Nova\Staff;
use App\Nova\Department;
use App\Nova\Lenses\TeacherStudents;
use App\Nova\Student;
use App\Nova\Teacher;
use App\Nova\User;
use Laravel\Nova\Menu\MenuGroup;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        $this->registerAssets();

        Nova::withBreadcrumbs();

        $this->renderMenu();
    }

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        Nova::routes()
                ->withAuthenticationRoutes()
                ->withPasswordResetRoutes()
                ->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewNova', function ($user) {
            return in_array($user->email, [
                //
            ]);
        });
    }

    /**
     * Get the dashboards that should be listed in the Nova sidebar.
     *
     * @return array
     */
    protected function dashboards()
    {
        return [
            new \App\Nova\Dashboards\Main,
        ];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        return [];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    private function renderMenu()
    {
        $departments = DepartmentModel::where('is_published', true)
            ->orderBy('order', 'desc')
            ->get();
        
        $result_departments = array();
        $departments_uri = Department::uriKey();
        foreach($departments as $category){
            $result_departments[] = MenuItem::link(
                $category->name, "/resources/$departments_uri/" . $category->id
            );
        }

        // $this->debugArea();

        Nova::mainMenu(function (Request $request) use($result_departments) {
            return [
                MenuSection::dashboard(Main::class)->icon('chart-bar'),

                MenuSection::make('System Security', [
                    MenuItem::resource(User::class),
                ])->icon('shield-check')->collapsable(),

                MenuSection::make('About Staff',[
                    MenuItem::resource(Staff::class),
                    MenuItem::resource(Department::class),
                    MenuItem::resource(Position::class),
                ])->icon('user-group')->collapsable(),

                MenuSection::make('Educational Part', [
                    MenuItem::resource(Teacher::class),
                    MenuItem::resource(Student::class),
                    MenuItem::resource(Group::class),
                    MenuItem::resource(Course::class),

                    MenuGroup::make('Reports', [
                        MenuItem::lens(Teacher::class, TeacherStudents::class),
                    ])->collapsable(),
                ])->icon('academic-cap')->collapsable(),
                
                MenuSection::make('Departments', $result_departments)
                    ->icon('template')
                    ->collapsable(),
                
                MenuSection::make('Other Data', [
                    MenuItem::resource(Address::class),
                ])->icon('collection')->collapsable(),
            ];
        });
    }

    private function registerAssets()
    {
        Nova::style('style', public_path('css/style.css'));
    }

    private function debugArea()
    {
        // $size = 5;
        // $dx = array(1, 2, 3, 4, 5);
        // $dy = array(4, 1, 8, 9, 10);
        // $chislitel = 0;
        // $znamenatel = 0;
        // for($i = 0; $i < $size; $i++){
        //     $chislitel += $dx[$i] * $dy[$i];
        // }
        // $sum_dx_2 = 0;
        // $sum_dy_2 = 0;
        // for($i = 0; $i < $size; $i++){
        //     $sum_dx_2 += $dx[$i] * $dx[$i];
        //     $sum_dy_2 += $dy[$i] * $dy[$i];
        // }
        // $znamenatel = $sum_dx_2 * $sum_dy_2;
        // // echo '<img src="https://answr.pro/uploads/content/xw/g9/xwG9Sv1idSHs4HcRuT9LoozRI4vZrhR2.jpg" alt="cor"/>';
        // echo '<br>';
        // echo 'dx = ' . print_r($dx, true);
        // echo '<br>';
        // echo 'dy = ' . print_r($dy, true);
        // echo '<br>';
        // echo 'Rxy = ' . $chislitel . ' / v' . $znamenatel . ' = ' . $chislitel / (sqrt($znamenatel));
        $row1 = array(1, 2, 3, 4, 5);
        $row2 = array(6, 7, 8, 9, 1);
        $size = 5;
        $datas = array();
        $temp = 0;
        // $row1[0] * $row2[$size-1];
        // $row1[0] * $row2[$size-2] + $row1[1] * $row2[$size-1];
        for($i = 1; $i <= 5; $i++){
            $temp2 = $i;
            while($temp2 != 0){
                $temp += $row1[0] * $row2[$size-$temp2];
                $temp2--;
            }
            $datas[] = $temp;
        }
        for($i = 3; $i >= 0; $i++){
            $temp2 = $i;
            while($temp2 != 0){
                $temp += $row2[$temp2] * $row2[$size-$temp2];
                $temp2--;
            }
            $datas[] = $temp;
        }
        print_r($datas);
        die();
    }
}
