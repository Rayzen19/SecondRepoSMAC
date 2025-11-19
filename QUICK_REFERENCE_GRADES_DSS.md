# Quick Reference: Grades & Decision Support System

## 🚀 Quick Access URLs

| Feature | URL | Route Name |
|---------|-----|------------|
| Grades | `/student/grades` | `student.grades.index` |
| Decision Support | `/student/decision-support` | `student.decision-support.index` |

## 📁 File Locations

### Controllers:
```
app/Http/Controllers/Student/
├── GradeController.php
└── DecisionSupportController.php
```

### Views:
```
resources/views/student/
├── grades/
│   └── index.blade.php
└── decision-support/
    └── index.blade.php
```

### Routes:
```
routes/web.php (lines ~277-282)
```

## 🎨 Design Highlights

### Grades Page:
- **Color Scheme**: Primary blue (#0d6efd), Success green, Warning yellow
- **Layout**: Bootstrap grid with responsive cards
- **Charts**: Chart.js donut chart for performance visualization
- **Icons**: Tabler Icons (ti-report-analytics, ti-chart-dots)

### Decision Support System:
- **Sections**:
  1. Performance Summary Cards (3 cards)
  2. Strengths & Weaknesses (2 columns)
  3. Personalized Recommendations
  4. Study Tips & Resources
  5. Performance Analysis
  6. Action Items (3 cards)

## 🔧 Configuration

### Grade Categories (in GradeController):
```php
'fq_grade' => First Quarter
'sq_grade' => Second Quarter  
'a_grade'  => Annual Grade
'f_grade'  => Final Grade
```

### Performance Assessment Types:
1. Activities
2. Quizzes
3. Assignment
4. Major Quiz
5. Exam
6. Recitation
7. Project

## 📊 Data Flow

### Grades Page:
```
Student → Academic Year → Student Enrollment → Subject Enrollments → Grades
```

### Decision Support:
```
Student → All Enrollments → Subject Enrollments → Analysis → Recommendations
```

## 🎯 Key Features

### Grades:
✅ Filter by year/semester  
✅ Color-coded grade badges  
✅ Average calculation  
✅ Performance chart  
✅ Print/export option  

### Decision Support:
✅ Overall performance metrics  
✅ Strengths identification (≥90)  
✅ Weaknesses identification (<80)  
✅ Personalized recommendations  
✅ Study tips  
✅ Action items  

## 🔐 Security

- Protected by `auth:student` middleware
- Only shows logged-in student's data
- No cross-student data access

## 📱 Responsive Design

Both pages are fully responsive:
- Desktop: Multi-column layouts
- Tablet: Adjusted grid
- Mobile: Stacked single-column layout

## 🎨 Color Codes

| Grade Range | Badge Color | Class |
|-------------|-------------|-------|
| 90-100 | Green | `bg-success` |
| 80-89 | Blue | `bg-primary` |
| < 80 | Yellow | `bg-warning` |

## 💡 Tips for Developers

1. **Add Real Performance Data**: Replace mock data in `GradeController` line 75-82
2. **Customize Thresholds**: Modify grade thresholds in controllers
3. **Add More Charts**: Include additional Chart.js visualizations
4. **Export Feature**: Implement PDF export using DomPDF or similar
5. **Caching**: Consider caching grade calculations for performance

## 🐛 Troubleshooting

### Chart not displaying?
- Check if Chart.js CDN is loading
- Verify JavaScript console for errors
- Ensure data is properly formatted

### No grades showing?
- Verify student has enrollments in selected year/semester
- Check if grades are recorded in database
- Confirm academic year is active

### Sidebar not highlighting?
- Check route names match in template.blade.php
- Verify `$routeIs()` function is working

## 📝 Maintenance

### Regular Updates:
- Review and update study tips
- Adjust recommendation logic based on feedback
- Update grade thresholds if school policy changes
- Add new performance metrics as needed
