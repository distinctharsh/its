 public function createInspector(Request $request)
    {
        try {
            // Start a database transaction
            DB::beginTransaction();

            // $captchaInput = $request->input('captcha');
            // $captchaSession = $request->session()->get('captcha_text');

            // if ($captchaInput !== $captchaSession) {
            //     return response()->json([
            //         'success' => false,
            //         'msg' => 'Captcha is not valid'
            //     ], 422);
            // }

            $nowIST = Carbon::now('Asia/Kolkata');
            // dd($nowIST);
            $validatedData = $request->validate([
                'name' => 'required|max:255',
                'gender' => 'required|exists:genders,id',
                'dob' => 'required|date|before:today',
                'nationality' => 'required|exists:nationalities,id',
                'place_of_birth' => 'required|max:255',
                'passport_number' => [
                    'required',
                    'max:255',
                    'unique:inspectors,passport_number',
                    'regex:/^[A-Z0-9]+$/',
                ],
                'unlp_number' => 'nullable|max:255|regex:/^[A-Z0-9]+$/',
                'rank' => 'required|exists:ranks,id',
                'designationId' => 'required|exists:designations,id',
                'qualifications' => 'required',
                'professional_experience' => 'required',
                'ib_clearance' => 'nullable|file|mimes:pdf|max:5120',
                'raw_clearance' => 'nullable|file|mimes:pdf|max:5120',
                'mea_clearance' => 'nullable|file|mimes:pdf|max:5120',
                
                // Routine fields
                'routine_category_id' => 'nullable|exists:inspection_categories,id',
                'routine_objection_department' => 'nullable|exists:departments,id',
                'date_of_joining' => 'nullable|date',
                'routine_deletion_date' => 'nullable|date',
                'routine_purpose_of_deletion' => 'nullable',
                // 'routine_status_id' => 'nullable|exists:statuses,id',
                'routine_remarks' => 'nullable|max:500',
                'routine_objection_document' => 'nullable|file|mimes:pdf|max:5120',
                
                
                // Challenge fields
                'challenge_category_id' => 'nullable|exists:inspection_categories,id',
                'challenge_objection_department' => 'nullable|exists:departments,id',
                'challenge_purpose_of_deletion' => 'nullable',
                'challenge_date_of_joining' => 'nullable|date',
                'challenge_deletion_date' => 'nullable|date',
                'challenge_remarks' => 'nullable|max:500',
                // 'challenge_status_id' => 'nullable|exists:statuses,id',
                'challenge_objection_document' => 'nullable|file|mimes:pdf|max:5120',


                'ib_status_id' => 'nullable|exists:statuses,id',
                'raw_status_id' => 'nullable|exists:statuses,id',
                'mea_status_id' => 'nullable|exists:statuses,id',
            ]);

     
            // Custom validation to ensure at least one section is filled
            if (empty($validatedData['routine_category_id']) && empty($validatedData['challenge_category_id'])) {
                return response()->json([
                    'success' => false,
                    'msg' => 'At least one inspection category (Routine or Challenge) must be filled.',
                ], 422);
            }

            // Custom validation to ensure 'date_of_joining' and 'routine_deletion_date' are required if 'routine_category_id' is present
            if (!empty($validatedData['routine_category_id'])) {
                $request->validate([
                    'routine_deletion_date' => 'required|date',
                ]);
            }
            if (!empty($validatedData['challenge_category_id'])) {
                $request->validate([
                    'challenge_deletion_date' => 'required|date',
                ]);
            }

            // Convert the date fields to the correct format (YYYY-MM-DD) using Carbon
            if (!empty($validatedData['routine_deletion_date'])) {
                $validatedData['routine_deletion_date'] = Carbon::createFromFormat('d-m-Y', $validatedData['routine_deletion_date'])->format('Y-m-d');
            }

            if (!empty($validatedData['challenge_deletion_date'])) {
                $validatedData['challenge_deletion_date'] = Carbon::createFromFormat('d-m-Y', $validatedData['challenge_deletion_date'])->format('Y-m-d');
            }

            // Handle file upload
            $ibClearancePath = null;
            if ($request->hasFile('ib_clearance')) {
                $ibClearancePath = $request->file('ib_clearance')->store('ib_clearances');
            }
            $rawClearancePath = null;
            if ($request->hasFile('raw_clearance')) {
                $rawClearancePath = $request->file('raw_clearance')->store('raw_clearances');
            }
            $meaClearancePath = null;
            if ($request->hasFile('mea_clearance')) {
                $meaClearancePath = $request->file('mea_clearance')->store('mea_clearances');
            }


            $routineObjectionDocumentPath = null;
            if ($request->hasFile('routine_objection_document')) {
                $routineObjectionDocumentPath = $request->file('routine_objection_document')->store('routine_objection_files');
            }
    
            $challengeObjectionDocumentPath = null;
            if ($request->hasFile('challenge_objection_document')) {
                $challengeObjectionDocumentPath = $request->file('challenge_objection_document')->store('challenge_objection_files');
            }


            // Create the inspector
            $inspector = Inspector::create([
                'name' => $validatedData['name'],
                'gender_id' => $validatedData['gender'],
                'dob' => $validatedData['dob'],
                'nationality_id' => $validatedData['nationality'],
                'place_of_birth' => $validatedData['place_of_birth'],
                'passport_number' => $validatedData['passport_number'],
                'unlp_number' => $validatedData['unlp_number'],
                'rank_id' => $validatedData['rank'],
                'designation_id' => $validatedData['designationId'],
                'qualifications' => $validatedData['qualifications'],
                'professional_experience' => $validatedData['professional_experience'],
                'ib_clearance' => $ibClearancePath,
                'raw_clearance' => $rawClearancePath,
                'mea_clearance' => $meaClearancePath,

                'ib_status_id' => $validatedData['ib_status_id'],
                'raw_status_id' => $validatedData['raw_status_id'],
                'mea_status_id' => $validatedData['mea_status_id'],

                'created_at' => $nowIST,

                'is_draft' => strtolower(auth()->user()->role->name) === 'user',
                'is_reverted' => 0,


            ]);

            // dd($request);

          



            // Create a new inspection record for routine if it's provided
            if (!empty($validatedData['routine_category_id'])) {
                $inspectionRoutine = Inspection::create([
                    'inspector_id' => $inspector->id,
                    'category_id' => $validatedData['routine_category_id'],
                    'objection_department_id' => $validatedData['routine_objection_department'],
                    // 'category_type_id' => $validatedData['routine_category_type_id'],
                    'date_of_joining' => $validatedData['date_of_joining'],
                    'deletion_date' => $validatedData['routine_deletion_date'],
                    'purpose_of_deletion' => $validatedData['routine_purpose_of_deletion'],
                    'routine_objection_document' => $routineObjectionDocumentPath,
                    'status_id' => 1,
                    'remarks' => $validatedData['routine_remarks'],
                    'created_by' => auth()->id(),
                    'created_at' => $nowIST,
                ]);
                
                $recordId = $inspectionRoutine->id;
                $changes = ['action' => 'New Inspection added'];
                LoggingService::logActivity($request, 'insert', 'inspections', $recordId, $changes);
            }
            
            // Create a new inspection record for challenge if it's provided
            if (!empty($validatedData['challenge_category_id'])) {
                $inspectionChallenge = Inspection::create([
                    'inspector_id' => $inspector->id,
                    'category_id' => $validatedData['challenge_category_id'],
                    'objection_department_id' => $validatedData['challenge_objection_department'],
                    // 'category_type_id' => $validatedData['challenge_category_type_id'],
                    'date_of_joining' => $validatedData['challenge_date_of_joining'],
                    'deletion_date' => $validatedData['challenge_deletion_date'],
                    'purpose_of_deletion' => $validatedData['challenge_purpose_of_deletion'],
                    'challenge_objection_document' => $challengeObjectionDocumentPath, 
                    'remarks' => $validatedData['challenge_remarks'],
                    // 'status_id' => $validatedData['challenge_status_id'],
                    'created_by' => auth()->id(),
                    'created_at' => $nowIST,
                ]);

                $recordId = $inspectionChallenge->id;
                $changes = ['action' => 'New Inspection added'];
                LoggingService::logActivity($request, 'insert', 'inspections', $recordId, $changes);
            }


            // Commit the transaction if everything goes well
            DB::commit();

            // Logging
            $recordId = $inspector->id;
            $changes = ['action' => 'New Inspector added'];
            LoggingService::logActivity($request, 'insert', 'inspectors', $recordId, $changes);



            // $request->session()->forget('captcha_text');

            return response()->json([
                'success' => true,
                'msg' => 'Inspector Created!'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {

            DB::rollBack();
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {

            DB::rollBack();
            if (isset($inspector)) {
                $inspector->forceDelete();
            }

            return response()->json([
                'success' => false,
                'msg' => $e->getMessage()
            ], 500);
        }
    }