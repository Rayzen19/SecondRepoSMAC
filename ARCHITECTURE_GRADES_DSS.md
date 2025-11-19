# System Architecture: Grades & Decision Support System

```
┌─────────────────────────────────────────────────────────────────────────┐
│                         STUDENT PORTAL SIDEBAR                          │
├─────────────────────────────────────────────────────────────────────────┤
│  📊 Dashboard                                                           │
│  📅 Academic Years                                                      │
│  📚 Subjects                                                            │
│  📈 Grades                          ← NEW                              │
│  📊 Decision Support System         ← NEW                              │
│  👤 My Profile                                                          │
│  🚪 Logout                                                              │
└─────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────┐
│                            GRADES PAGE                                  │
├─────────────────────────────────────────────────────────────────────────┤
│  ┌──────────────────────────────────────────────────────────────────┐  │
│  │ 🔽 Year/Semester Filter                                          │  │
│  │ [Academic Year ▼] [Semester ▼]                                   │  │
│  └──────────────────────────────────────────────────────────────────┘  │
│                                                                          │
│  ┌──────────────────────────────────────────────────────────────────┐  │
│  │ GRADES TABLE                                                      │  │
│  ├──────────────┬──────────────────┬──────────┤                      │  │
│  │ Subject Code │ Subject Name     │ Grade    │                      │  │
│  ├──────────────┼──────────────────┼──────────┤                      │  │
│  │ SCIENCE1     │ SCIENCE          │ [85]     │                      │  │
│  │ MATH1        │ MATH             │ [85]     │                      │  │
│  │ FILIPINO1    │ FILIPINO         │ [85]     │                      │  │
│  │ ENGLISH      │ ENGLISH          │ [85]     │                      │  │
│  └──────────────┴──────────────────┴──────────┘                      │  │
│                                            Average: 85                │  │
└─────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────┐
│                         PERFORMANCE SECTION                             │
├─────────────────────────────────────────────────────────────────────────┤
│  Legend: [🟡Activities] [🔵Quizzes] [🔵Assignment] [⚫Major Quiz]       │
│          [⚫Exam] [🔴Recitation] [🟢Project]                             │
│                                                                          │
│  Left Column:                      Right Column:                        │
│  ┌────────────────────────┐        ┌────────────────────────┐          │
│  │ Activities      60%    │        │                        │          │
│  │ Quizzes         60%    │        │    📊 Donut Chart      │          │
│  │ Assignment      60%    │        │                        │          │
│  │ Major Quiz      60%    │        │   (Performance Data)   │          │
│  │ Exam            60%    │        │                        │          │
│  │ Recitation      60%    │        │                        │          │
│  │ Project         60%    │        │                        │          │
│  └────────────────────────┘        └────────────────────────┘          │
│                                                                          │
│                                            [📄 Report Button]           │
└─────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────┐
│                    DECISION SUPPORT SYSTEM PAGE                         │
├─────────────────────────────────────────────────────────────────────────┤
│  ┌────────────────┐  ┌────────────────┐  ┌────────────────┐           │
│  │   Overall      │  │     Strong     │  │     Needs      │           │
│  │   Average      │  │    Subjects    │  │  Improvement   │           │
│  │      85        │  │       3        │  │       2        │           │
│  └────────────────┘  └────────────────┘  └────────────────┘           │
│                                                                          │
│  ┌──────────────────────────────┐  ┌──────────────────────────────┐   │
│  │ 🏆 Academic Strengths        │  │ 🎯 Areas for Improvement     │   │
│  ├──────────────────────────────┤  ├──────────────────────────────┤   │
│  │ • Subject A       [95]       │  │ • Subject X       [75]       │   │
│  │ • Subject B       [92]       │  │ • Subject Y       [78]       │   │
│  │ • Subject C       [90]       │  │                              │   │
│  └──────────────────────────────┘  └──────────────────────────────┘   │
│                                                                          │
│  ┌──────────────────────────────────────────────────────────────────┐  │
│  │ 💡 Personalized Recommendations                                  │  │
│  ├──────────────────────────────────────────────────────────────────┤  │
│  │ 1️⃣ Excellent performance! Continue maintaining study habits     │  │
│  │ 2️⃣ Consider taking advanced or honors classes                   │  │
│  │ 3️⃣ Focus additional study time on: Subject X, Subject Y         │  │
│  └──────────────────────────────────────────────────────────────────┘  │
│                                                                          │
│  ┌──────────────────────────────────────────────────────────────────┐  │
│  │ 📚 Study Tips & Resources                                        │  │
│  ├──────────────────────────────────────────────────────────────────┤  │
│  │ Time Management:           Effective Study Strategies:           │  │
│  │ • Create weekly schedule   • Take detailed notes                 │  │
│  │ • Use Pomodoro Technique   • Practice active recall              │  │
│  │ • Prioritize difficult     • Form study groups                   │  │
│  │ • Avoid cramming           • Ask teachers for help               │  │
│  └──────────────────────────────────────────────────────────────────┘  │
│                                                                          │
│  ┌──────────────────────────────────────────────────────────────────┐  │
│  │ 📊 Performance Analysis                                          │  │
│  ├──────────────────────────────────────────────────────────────────┤  │
│  │ ✅ Very Good Performance! You're doing well!                     │  │
│  │                                                                   │  │
│  │ Quick Stats:                                                      │  │
│  │ [Total: 8] [Strengths: 3] [Needs Work: 2] [Grade: B+]           │  │
│  └──────────────────────────────────────────────────────────────────┘  │
│                                                                          │
│  ┌──────────────────────────────────────────────────────────────────┐  │
│  │ ✓ Suggested Action Items                                         │  │
│  ├──────────────────────────────────────────────────────────────────┤  │
│  │ [📅 Schedule Review] [👥 Join Study Groups] [❓ Seek Support]    │  │
│  └──────────────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────────────┘
```

## Data Flow Diagram

```
┌─────────────┐
│   Student   │
│   Login     │
└──────┬──────┘
       │
       ▼
┌─────────────────────────────────────────────┐
│          Student Dashboard                  │
│  ┌────────────┐    ┌────────────────────┐  │
│  │   Grades   │    │ Decision Support   │  │
│  │   Route    │    │      System        │  │
│  └──────┬─────┘    └─────────┬──────────┘  │
└─────────┼───────────────────┼──────────────┘
          │                   │
          ▼                   ▼
┌──────────────────┐  ┌──────────────────┐
│ GradeController  │  │DecisionSupport   │
│                  │  │   Controller     │
└────────┬─────────┘  └────────┬─────────┘
         │                     │
         ▼                     ▼
┌─────────────────────────────────────────┐
│          Database Models                │
├─────────────────────────────────────────┤
│ • AcademicYear                          │
│ • StudentEnrollment                     │
│ • SubjectEnrollment (grades)            │
│ • AcademicYearStrandSubject             │
└─────────────────────────────────────────┘
         │
         ▼
┌─────────────────────────────────────────┐
│          Blade Views                    │
├─────────────────────────────────────────┤
│ • grades/index.blade.php                │
│ • decision-support/index.blade.php      │
└─────────────────────────────────────────┘
```

## Component Breakdown

### Grades Page Components:
1. **Filter Section** - Academic year & semester selection
2. **Grades Table** - Subject codes, names, and grades
3. **Average Display** - Calculated average grade
4. **Performance Legend** - Color-coded assessment types
5. **Performance Bars** - Visual representation of each assessment
6. **Donut Chart** - Interactive Chart.js visualization
7. **Report Button** - Print/export functionality

### Decision Support System Components:
1. **Summary Cards** (3) - Key performance metrics
2. **Strengths List** - Top performing subjects
3. **Weaknesses List** - Subjects needing attention
4. **Recommendations** - Personalized suggestions
5. **Study Tips** - Time management & study strategies
6. **Performance Analysis** - Detailed performance breakdown
7. **Quick Stats** - Statistical overview
8. **Action Items** - Suggested next steps

## Color Coding System

```
Grade Ranges:
┌──────────────┬────────────┬───────────────┐
│ Range        │ Color      │ Meaning       │
├──────────────┼────────────┼───────────────┤
│ 90-100       │ Green      │ Excellent     │
│ 85-89        │ Blue       │ Very Good     │
│ 80-84        │ Blue       │ Good          │
│ 75-79        │ Yellow     │ Fair          │
│ Below 75     │ Red        │ Needs Work    │
└──────────────┴────────────┴───────────────┘

Performance Categories:
┌──────────────────┬────────────────┐
│ Category         │ Color          │
├──────────────────┼────────────────┤
│ Activities       │ 🟡 Warning     │
│ Quizzes          │ 🔵 Primary     │
│ Assignment       │ 🔵 Info        │
│ Major Quiz       │ ⚫ Dark        │
│ Exam             │ ⚫ Secondary   │
│ Recitation       │ 🔴 Danger      │
│ Project          │ 🟢 Success     │
└──────────────────┴────────────────┘
```

## File Structure

```
NEWSMAC/
├── app/
│   └── Http/
│       └── Controllers/
│           └── Student/
│               ├── GradeController.php           ← NEW
│               └── DecisionSupportController.php ← NEW
│
├── resources/
│   └── views/
│       └── student/
│           ├── components/
│           │   └── template.blade.php            ← MODIFIED
│           ├── grades/
│           │   └── index.blade.php               ← NEW
│           └── decision-support/
│               └── index.blade.php               ← NEW
│
├── routes/
│   └── web.php                                   ← MODIFIED
│
└── Documentation/
    ├── README_GRADES_DSS.md                      ← NEW
    ├── QUICK_REFERENCE_GRADES_DSS.md             ← NEW
    ├── IMPLEMENTATION_SUMMARY_GRADES_DSS.md      ← NEW
    └── ARCHITECTURE_GRADES_DSS.md                ← NEW (this file)
```
