-- Update all health_treatments records where attended_by is NULL
UPDATE health_treatments 
SET attended_by = 'School Nurse' 
WHERE attended_by IS NULL;

-- Show updated records
SELECT id, attended_by, date 
FROM health_treatments 
ORDER BY id 
LIMIT 10;
