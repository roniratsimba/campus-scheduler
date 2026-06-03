classDiagram

class User {
    +id : bigint
    +email : string
    +password : string
    +role : string
    +createdAt : datetime
    +updatedAt : datetime
}

class Teacher {
    +id : bigint
    +firstName : string
    +lastName : string
    +email : string
    +phone : string
}

class Subject {
    +id : bigint
    +name : string
}

class AcademicLevel {
    +id : bigint
    +code : string
}

class Program {
    +id : bigint
    +code : string
    +name : string
}

class AcademicGroup {
    +id : bigint
    +groupNumber : string
    +displayName : string
}

class RoomType {
    +id : bigint
    +name : string
}

class Room {
    +id : bigint
    +name : string
}

class TimeSlot {
    +id : bigint
    +label : string
    +startTime : time
    +endTime : time
}

class ScheduleWeek {
    +id : bigint
    +startDate : date
    +endDate : date
    +status : string
    +publishedAt : datetime
}

class CourseSession {
    +id : bigint
    +dayOfWeek : integer
    +createdAt : datetime
    +updatedAt : datetime
}

AcademicLevel "1" --> "0..*" AcademicGroup

Program "1" --> "0..*" AcademicGroup

RoomType "1" --> "0..*" Room

Teacher "1" --> "0..*" CourseSession

Subject "1" --> "0..*" CourseSession

Room "1" --> "0..*" CourseSession

TimeSlot "1" --> "0..*" CourseSession

ScheduleWeek "1" --> "0..*" CourseSession

CourseSession "*" --> "*" AcademicGroup