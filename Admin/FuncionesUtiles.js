function ReemplazarTodo(unString, CaracteresXReemplazar, CaracteresReemplazo)
{
    while (unString.indexOf(CaracteresXReemplazar) >= 0)
        unString = unString.replace(CaracteresXReemplazar, CaracteresReemplazo);

    return unString;
} // function ReemplazarTodo(unString, CaracteresXReemplazar, CaracteresReemplazo)
