<?php

namespace App\Services\Calendar;

use Eluceo\iCal\Domain\Entity\Calendar;
use Eluceo\iCal\Domain\Entity\Event;
use Eluceo\iCal\Domain\Enum\EventStatus;
use Eluceo\iCal\Domain\ValueObject\UniqueIdentifier;
use Eluceo\iCal\Domain\ValueObject\TimeSpan;
use Eluceo\iCal\Presentation\Factory\CalendarFactory;
use Eluceo\iCal\Domain\ValueObject\DateTime;
use App\Models\Tasks;

final class GenerateCalendar
{
    /**
     * Создаем календарь
     * @param Tasks $task
     * @param bool $flagDeleted удаление задачи
     * @return void
     */
    final public function createTaskCalendar(Tasks $task, bool $flagDeleted = false): void
    {
        $events = [];
        $tasks = Tasks::whereNotNull('calendar_uid')
            ->whereIn('status', ['в работе', 'ожидает', 'просрочена'])
            ->where('lawyer', $task->lawyer)
            ->orderBy('created_at', 'ASC')
            ->get();

        /** @var Tasks $element */
        // Записываем предыдущие задачи
        if ($tasks) {
            foreach ($tasks as $element) {                
                if ($element->id !== $task->id) {
                    //if($task->date['value'] == true){
                    $events[] = $this->createEvent($element);
                    //}
                }
            }
        }
        // Записываем новую задачу
        if (!$flagDeleted) $events[] = $this->createEvent($task, true);

        if (!empty($events)) {
            // Создать объект домена календаря
            $calendar = new Calendar($events);
            // Преобразование объекта домена в компонент iCalendar
            $componentFactory = new CalendarFactory();
            $calendarComponent = $componentFactory->createCalendar($calendar);

            $dir = storage_path("app/public/calendar/user_{$task->lawyer}");
            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }
            file_put_contents($dir . "/calendar.ics", (string) $calendarComponent);
            
        }
    }

    /**
     * Создаем события (добавление задачи) для календаря
     * @param Tasks $task
     * @param boolean $updateUid
     * @return Event
     */
    private function createEvent(Tasks $task, bool $updateUid = false): Event
    {
        $eventUid = md5('task-' . $task->lawyer . '-' . $task->id);
        $uniqueIdentifier = new UniqueIdentifier($eventUid);
        // Обновляем calendar_uid
        if ($updateUid) $task->update(['calendar_uid' => $eventUid]);
        $description = (!empty($task->description)) ? $task->description : 'Описание отсувствует';
        //dd(new DateTime(\DateTimeImmutable::createFromFormat('Y-m-d H:i',  (string) $task->date['value']), true));
        // Создаем сущность домена события
        $dateStart = new DateTime(\DateTimeImmutable::createFromFormat('Y-m-d H:i',  (string) $task->date['value']), false);
        $dateEnd = new DateTime(\DateTimeImmutable::createFromFormat('Y-m-d H:i',  $task->date['rawValue']->addMinutes($task->duration)->format('Y-m-d H:i')), false);
        $event = (new Event($uniqueIdentifier))
            ->setStatus(EventStatus::CONFIRMED())
            ->setSummary($task->name)
            ->setDescription($description)
            ->setOccurrence(
                new TimeSpan($dateStart, $dateEnd)
            );

        return $event;
    }
}
