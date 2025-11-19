# Term-Based Scoring: Visual Guide

## System Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                     ACADEMIC YEAR                            │
│                      2024-2025                               │
└───────────────────────┬─────────────────────────────────────┘
                        │
        ┌───────────────┴───────────────┐
        │                               │
        ▼                               ▼
┌───────────────┐              ┌───────────────┐
│ 1st SEMESTER  │              │ 2nd SEMESTER  │
│ (quarter='1st')│              │ (quarter='2nd')│
└───────┬───────┘              └───────┬───────┘
        │                               │
   ┌────┴────┐                     ┌────┴────┐
   │         │                     │         │
   ▼         ▼                     ▼         ▼
┌──────┐  ┌──────┐              ┌──────┐  ┌──────┐
│MIDTERM│ │FINALS│              │MIDTERM│ │FINALS│
│term= │  │term= │              │term= │  │term= │
│'mid- │  │'fin- │              │'mid- │  │'fin- │
│term' │  │als'  │              │term' │  │als'  │
└──┬───┘  └──┬───┘              └──┬───┘  └──┬───┘
   │         │                     │         │
   │         │                     │         │
   ▼         ▼                     ▼         ▼
Assessments Assessments        Assessments Assessments
WW1-M      WW4-F               WW1-M      WW4-F
WW2-M      WW5-F               WW2-M      WW5-F
WW3-M      WW6-F               WW3-M      WW6-F
PT1-M      PT3-F               PT1-M      PT3-F
PT2-M      PT4-F               PT2-M      PT4-F
QA-M       QA-F                QA-M       QA-F
```

## Before vs After

### BEFORE (Old System)
```
subject_records
├── quarter: '1st' or '2nd'
├── type: 'written work', 'performance task', 'quarterly assessment'
└── All assessments mixed together per semester
```

**Example Data (Old):**
| ID | Name | Quarter | Type | Term |
|----|------|---------|------|------|
| 1  | WW1  | 1st     | written work | NULL |
| 2  | WW2  | 1st     | written work | NULL |
| 3  | PT1  | 1st     | performance task | NULL |
| 4  | QA   | 1st     | quarterly assessment | NULL |

**Problem:** No way to distinguish midterm from finals assessments!

### AFTER (New System)
```
subject_records
├── quarter: '1st' or '2nd' (semester)
├── term: 'midterm' or 'finals' (NEW!)
├── type: 'written work', 'performance task', 'quarterly assessment'
└── Assessments separated by term
```

**Example Data (New):**
| ID | Name | Quarter | Term | Type |
|----|------|---------|------|------|
| 1  | WW1  | 1st     | midterm | written work |
| 2  | WW2  | 1st     | midterm | written work |
| 3  | WW3  | 1st     | finals | written work |
| 4  | WW4  | 1st     | finals | written work |
| 5  | PT1  | 1st     | midterm | performance task |
| 6  | PT2  | 1st     | finals | performance task |
| 7  | QA-M | 1st     | midterm | quarterly assessment |
| 8  | QA-F | 1st     | finals | quarterly assessment |

**Solution:** Clear separation of midterm and finals!

## User Interface Flow

### Adding Assessment with Term

```
┌──────────────────────────────────────────┐
│  Student: Juan Dela Cruz                 │
│  Subject: Mathematics                     │
│  ┌────────────┐  ┌───────────┐          │
│  │Grade Level▼│  │Term Filter▼│          │
│  │    11      │  │  Midterm   │ ◄──── 1. Teacher selects filter
│  └────────────┘  └───────────┘          │
└──────────────────────────────────────────┘

           │
           │ 2. Click "Add WW" button
           ▼

┌──────────────────────────────────────────┐
│  Add Written Work                    [X] │
│                                          │
│  Name: _________________________        │
│                                          │
│  Description: __________________        │
│                                          │
│  Date: [2025-10-25]                     │
│                                          │
│  Max Score: [100]                       │
│                                          │
│  Term: [Midterm ▼] ◄──── 3. Auto-selected!
│        • Midterm                         │
│        • Finals                          │
│                                          │
│  [Cancel]  [Submit]                     │
└──────────────────────────────────────────┘

           │
           │ 4. Submit
           ▼

┌──────────────────────────────────────────┐
│  ✓ Assessment created successfully!      │
│                                          │
│  Database Record:                        │
│  - quarter: '1st'                       │
│  - term: 'midterm' ◄──── Saved!         │
│  - type: 'written work'                 │
└──────────────────────────────────────────┘
```

## Filtering Behavior

### Scenario 1: View Midterm Only
```
URL: ?term=midterm

┌────────────────────────────────┐
│  Filter: [Midterm ▼]          │
└────────────────────────────────┘

Database Query:
WHERE term = 'midterm'

Results Shown:
├─ WW1 (midterm)     ✓ Shown
├─ WW2 (midterm)     ✓ Shown
├─ WW3 (finals)      ✗ Hidden
├─ PT1 (midterm)     ✓ Shown
└─ QA-M (midterm)    ✓ Shown

Grade Calculation:
Based ONLY on midterm assessments
```

### Scenario 2: View Finals Only
```
URL: ?term=finals

┌────────────────────────────────┐
│  Filter: [Finals ▼]           │
└────────────────────────────────┘

Database Query:
WHERE term = 'finals'

Results Shown:
├─ WW1 (midterm)     ✗ Hidden
├─ WW3 (finals)      ✓ Shown
├─ WW4 (finals)      ✓ Shown
├─ PT2 (finals)      ✓ Shown
└─ QA-F (finals)     ✓ Shown

Grade Calculation:
Based ONLY on finals assessments
```

### Scenario 3: View All Terms
```
URL: ?term= (or no parameter)

┌────────────────────────────────┐
│  Filter: [All Terms ▼]        │
└────────────────────────────────┘

Database Query:
No term filter applied

Results Shown:
├─ WW1 (midterm)     ✓ Shown
├─ WW2 (midterm)     ✓ Shown
├─ WW3 (finals)      ✓ Shown
├─ WW4 (finals)      ✓ Shown
├─ PT1 (midterm)     ✓ Shown
├─ PT2 (finals)      ✓ Shown
├─ QA-M (midterm)    ✓ Shown
└─ QA-F (finals)     ✓ Shown

Grade Calculation:
Based on ALL assessments
```

## Grade Calculation Example

### Example Student: Maria Santos - Math Subject

**First Semester - Midterm**
```
Written Work (WW):
├─ WW1: 45/50 ─┐
└─ WW2: 40/50 ─┤─► Total: 85/100 = 85% × 0.25 = 21.25
               │
Performance Task (PT):
├─ PT1: 90/100 ┤─► Total: 90/100 = 90% × 0.50 = 45.00
               │
Quarterly Assessment (QA):
└─ QA: 80/100 ──┤─► Total: 80/100 = 80% × 0.25 = 20.00
                │
                └─► Midterm Grade: 21.25 + 45.00 + 20.00 = 86.25
```

**First Semester - Finals**
```
Written Work (WW):
├─ WW3: 48/50 ─┐
└─ WW4: 47/50 ─┤─► Total: 95/100 = 95% × 0.25 = 23.75
               │
Performance Task (PT):
├─ PT2: 85/100 ┤─► Total: 85/100 = 85% × 0.50 = 42.50
               │
Quarterly Assessment (QA):
└─ QA: 88/100 ──┤─► Total: 88/100 = 88% × 0.25 = 22.00
                │
                └─► Finals Grade: 23.75 + 42.50 + 22.00 = 88.25
```

**First Semester Final Grade**
```
Average: (86.25 + 88.25) / 2 = 87.25
Description: Very Satisfactory
```

## Database Schema Diagram

```
┌─────────────────────────────────────────┐
│         subject_records                 │
├─────────────────────────────────────────┤
│ id (PK)                                 │
│ academic_year_strand_subject_id (FK)    │
│ name                                     │
│ description                              │
│ max_score                                │
│ type                                     │
│ quarter ('1st', '2nd')                  │
│ term ('midterm', 'finals') ◄── NEW!    │
│ date_given                               │
│ remarks                                  │
│ created_at                               │
│ updated_at                               │
│ deleted_at                               │
└─────────────────────────────────────────┘
              │
              │ 1:N
              ▼
┌─────────────────────────────────────────┐
│      subject_record_results             │
├─────────────────────────────────────────┤
│ id (PK)                                 │
│ subject_record_id (FK)                  │
│ student_id (FK)                          │
│ raw_score                                │
│ base_score                               │
│ final_score                              │
│ remarks                                  │
│ description                              │
│ date_submitted                           │
│ created_at                               │
│ updated_at                               │
└─────────────────────────────────────────┘
```

## Real-World Example Timeline

```
📅 Academic Year 2024-2025, First Semester

Week 1-8: MIDTERM PERIOD
├─ Oct 01: Created WW1 (midterm)
├─ Oct 08: Created WW2 (midterm)
├─ Oct 15: Created PT1 (midterm)
├─ Oct 22: Created WW3 (midterm)
└─ Oct 29: Created QA-Midterm (midterm)

Week 9-16: FINALS PERIOD
├─ Nov 05: Created WW4 (finals)
├─ Nov 12: Created WW5 (finals)
├─ Nov 19: Created PT2 (finals)
├─ Nov 26: Created WW6 (finals)
└─ Dec 03: Created QA-Finals (finals)

Result:
✓ 5 assessments for Midterm
✓ 5 assessments for Finals
✓ Each can be viewed separately
✓ Grades calculated independently
```

## Summary

| Feature | Old System | New System |
|---------|-----------|------------|
| **Term Separation** | ❌ No | ✅ Yes |
| **Midterm Tracking** | ❌ Mixed with finals | ✅ Separate |
| **Finals Tracking** | ❌ Mixed with midterm | ✅ Separate |
| **Filtering** | ❌ Only by semester | ✅ By semester AND term |
| **Grade Calculation** | ⚠️ All assessments together | ✅ Can calculate per term |
| **Reporting** | ⚠️ Limited | ✅ Term-specific reports possible |

## Key Takeaways

1. ✅ **Two-Level Structure**: Semester (quarter) → Term
2. ✅ **Clear Separation**: Midterm and finals are distinct
3. ✅ **Flexible Filtering**: View by term or view all
4. ✅ **Accurate Grading**: Calculate grades per term
5. ✅ **Better Organization**: Teachers can manage periods separately
6. ✅ **Backward Compatible**: Old records still work (term = NULL)

## Legend

- 📅 Calendar/Date
- ✓ Included/Shown
- ✗ Excluded/Hidden
- ◄── Important point
- ▼ Dropdown menu
- [X] Close button
- (FK) Foreign Key
- (PK) Primary Key
